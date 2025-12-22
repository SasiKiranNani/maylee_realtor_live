<?php

namespace App\Filament\Pages;

use App\Models\SlotBooking;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SlotCalendarPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Calendar Slots';
    protected static ?string $title = '';
    protected static ?string $navigationGroup  = 'Slot Management';
    protected static string $view = 'filament.pages.slot-calendar-page';

    protected static ?int $navigationSort = 4;
    
    public $currentMonth;
    public $selectedDates = [];
    public bool $showBulkCreateForm = false;
    public $bulkSlotData = [
        'available_time_slots' => [],
        'dates' => [],
        'capacity' => 1,
        'notes' => '',
    ];
    public $showViewSlots = false;
    public Collection $viewingSlots;

    public function mount(): void
    {
        $this->viewingSlots = collect();
        $this->currentMonth = now()->startOfMonth();
        $this->bulkSlotData['available_time_slots'] = TimeSlot::select('id', 'start_time', 'end_time')
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'label' => Carbon::parse($slot->start_time)->format('g:i A') . ' - ' . Carbon::parse($slot->end_time)->format('g:i A'),
                ];
            })
            ->toArray();
    }

    public function navigateMonth(string $direction): void
    {
        $this->currentMonth = $direction === 'prev'
            ? $this->currentMonth->copy()->subMonth()
            : $this->currentMonth->copy()->addMonth();
    }

    public function toggleDateSelection(string $date): void
    {
        if (in_array($date, $this->selectedDates)) {
            $this->selectedDates = array_values(array_diff($this->selectedDates, [$date]));
        } else {
            $this->selectedDates[] = $date;
        }
    }

    public function openBulkCreateModal(): void
    {
        if (empty($this->selectedDates)) {
            $this->addError('selectedDates', 'Please select at least one date.');
            return;
        }

        // Fetch existing slots for selected dates, handling datetime column
        $existingSlots = SlotBooking::with('slot')
            ->where(function ($query) {
                foreach ($this->selectedDates as $dateStr) {
                    $query->orWhereRaw('DATE(`date`) = ?', [$dateStr]);
                }
            })
            ->get()
            ->groupBy(function ($item) {
                return substr($item->date, 0, 10);
            });

        $this->bulkSlotData['dates'] = [];

        foreach ($this->selectedDates as $date) {
            $dateSlots = $existingSlots->get($date, collect());
            $existingSlotIds = $dateSlots->pluck('time_slot_id')->unique()->toArray();

            $this->bulkSlotData['dates'][$date] = [
                'existing_slots' => $dateSlots,
                'selected_slots' => $existingSlotIds, // Pre-select existing time slot IDs
            ];
        }

        $this->dispatch('open-modal', id: 'bulkCreateModal');
    }

    public function closeBulkCreateModal(): void
    {
        $this->dispatch('close-modal', id: 'bulkCreateModal');
    }

    public function createBulkSlots(): void
    {
        $hasSelections = false;
        foreach ($this->bulkSlotData['dates'] as $date => $data) {
            if (!empty($data['selected_slots'])) {
                $hasSelections = true;
                foreach ($data['selected_slots'] as $slotId) {
                    SlotBooking::updateOrCreate(
                        ['date' => $date, 'time_slot_id' => $slotId],
                        [
                            'is_booked' => false,
                            'capacity' => $this->bulkSlotData['capacity'],
                            'notes' => $this->bulkSlotData['notes'],
                        ]
                    );
                }
            }
        }

        if (!$hasSelections) {
            $this->addError('bulkSlotData', 'Please select at least one time slot for one of the dates.');
            return;
        }

        $this->dispatch('notify', 'Slots created successfully.');

        $this->dispatch('close-modal', id: 'bulkCreateModal');
        $this->resetBulkData();
        $this->selectedDates = [];
    }

    public function resetBulkData(): void
    {
        $this->bulkSlotData['dates'] = [];
        $this->bulkSlotData['capacity'] = 1;
        $this->bulkSlotData['notes'] = '';
    }

    public function getCalendarData(): Collection
    {
        $start = $this->currentMonth->copy()->startOfMonth()->startOfWeek();
        $end = $this->currentMonth->copy()->endOfMonth()->endOfWeek();

        $days = collect();

        $bookings = SlotBooking::selectRaw('DATE(date) as date_key, COUNT(*) as slots_count, SUM(CASE WHEN is_booked = 0 THEN 1 ELSE 0 END) as available_slots')
            ->whereRaw('DATE(date) BETWEEN ? AND ?', [$start->toDateString(), $end->toDateString()])
            ->groupBy(DB::raw('DATE(date)'))
            ->get()
            ->keyBy('date_key');

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateString = $date->toDateString();
            $info = $bookings->get($dateString);

            $days->push([
                'date' => $date->copy(),
                'date_string' => $dateString,
                'is_today' => $date->isToday(),
                'is_current_month' => $date->month === $this->currentMonth->month,
                'is_selected' => in_array($dateString, $this->selectedDates),
                'slots_count' => $info?->slots_count ?? 0,
                'available_slots' => $info?->available_slots ?? 0,
            ]);
        }

        return $days;
    }

    public function viewSelectedSlots(): void
    {
        $this->viewingSlots = SlotBooking::with('slot')
            ->where(function ($query) {
                foreach ($this->selectedDates as $dateStr) {
                    $query->orWhereRaw('DATE(`date`) = ?', [$dateStr]);
                }
            })
            ->orderByRaw('DATE(date), time_slot_id')
            ->get();

        $this->dispatch('open-modal', id: 'viewSlotsModal');
    }

    public function deleteSlot($slotId): void
    {
        SlotBooking::where('id', $slotId)->delete();

        $this->viewingSlots = $this->viewingSlots->reject(fn($slot) => $slot->id == $slotId);

        $this->dispatch('notify', [
            'title' => 'Slot Deleted',
            'body' => 'The selected slot was deleted successfully.',
            'status' => 'danger',
        ]);
    }

    public function deleteSelectedSlots(): void
    {
        SlotBooking::where(function ($query) {
            foreach ($this->selectedDates as $dateStr) {
                $query->orWhereRaw('DATE(`date`) = ?', [$dateStr]);
            }
        })->delete();

        $this->dispatch('notify', [
            'title' => 'Slots Deleted',
            'body' => 'All slots for selected date(s) have been deleted.',
            'status' => 'danger',
        ]);

        $this->selectedDates = [];
        $this->resetBulkData();
    }
}
