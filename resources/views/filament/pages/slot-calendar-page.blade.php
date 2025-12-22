<x-filament-panels::page>
    <x-slot name="title"></x-slot>
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h2 style="font-size:24px;font-weight:700;color:#111827;margin:0;">Calendar View</h2>
            <p style="font-size:14px;color:#6B7280;margin-top:4px;">Select dates to create time slots</p>
        </div>

        <div style="display:flex;gap:8px;">
            <x-filament::button wire:click="openBulkCreateModal" icon="heroicon-o-plus" color="primary" :disabled="empty($selectedDates)"
                style="background-color:#2563EB;color:white;border:none;padding:10px 16px;border-radius:8px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                Create Slots for {{ count($selectedDates) }} Selected Dates
            </x-filament::button>

            <a href="{{ url('/admin/time-slots') }}"
                style="background-color:#F3F4F6;color:#111827;border:1px solid #D1D5DB;padding:10px 16px;border-radius:8px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                List View
            </a>
        </div>
    </div>

    <!-- Modal (always rendered) -->
    <x-filament::modal id="bulkCreateModal" width="3xl" :close-button="true" :slide-over="false" wire:ignore.self>
        <x-slot name="heading">Assign Time Slots</x-slot>
        <x-slot name="description">
            Select one or more time slots to assign to {{ count($selectedDates) }} selected date(s). Pre-selected
            checkboxes indicate existing slots.
        </x-slot>

        <form wire:submit.prevent="createBulkSlots">
            <div style="padding:16px;">
                <h4 style="font-size:15px;font-weight:600;margin-bottom:8px;">Select Time Slots</h4>

                <div style="max-height:400px;overflow-y:auto;">
                    @foreach ($bulkSlotData['dates'] as $date => $data)
                        <div style="margin-bottom:16px;border-bottom:1px solid #E5E7EB;padding-bottom:12px;">
                            <h5 style="font-weight:600;margin-bottom:6px;color:#111827;">
                                {{ \Carbon\Carbon::parse($date)->format('M j, Y') }}
                            </h5>

                            @if ($data['existing_slots']->isNotEmpty())
                                <div style="margin-bottom:8px;font-size:13px;color:#6B7280;">
                                    Existing slots (pre-selected):
                                    @foreach ($data['existing_slots'] as $slot)
                                        <span
                                            style="display:inline-block;background:#DBEAFE;color:#1E40AF;padding:2px 6px;border-radius:4px;font-size:11px;margin-right:4px;margin-bottom:2px;">
                                            {{ \Carbon\Carbon::parse($slot->slot?->start_time)->format('g:i A') }} -
                                            {{ \Carbon\Carbon::parse($slot->slot?->end_time)->format('g:i A') }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p style="font-size:12px;color:#9CA3AF;margin-bottom:8px;">No existing slots for this
                                    date.</p>
                            @endif

                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;">
                                @foreach ($bulkSlotData['available_time_slots'] as $slot)
                                    <label
                                        style="display:flex;align-items:center;gap:8px;cursor:pointer;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;padding:8px 12px;transition:background-color 0.2s;">
                                        <input type="checkbox"
                                            wire:model="bulkSlotData.dates.{{ $date }}.selected_slots"
                                            value="{{ $slot['id'] }}"
                                            style="accent-color:#2563EB;width:16px;height:16px;">
                                        <span style="font-size:14px;color:#111827;">{{ $slot['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:20px;">
                    <div>
                        <label style="block text-sm font-semibold text-gray-700 mb-1">Capacity (applies to new
                            slots)</label>
                        <input type="number" min="1" wire:model="bulkSlotData.capacity"
                            style="width:100%;padding:8px 10px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;color:#111827;">
                    </div>

                    <div>
                        <label style="block text-sm font-semibold text-gray-700 mb-1">Notes (applies to new
                            slots)</label>
                        <textarea wire:model="bulkSlotData.notes" rows="2" placeholder="Optional notes..."
                            style="width:100%;padding:8px 10px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;color:#111827;"></textarea>
                    </div>
                </div>

                <div
                    style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;border-top:1px solid #E5E7EB;pt:16px;">
                    <x-filament::button type="button" wire:click="closeBulkCreateModal" color="gray">
                        Cancel
                    </x-filament::button>
                    <x-filament::button type="submit" color="primary">
                        Create/Update Slots
                    </x-filament::button>
                </div>
            </div>
        </form>
    </x-filament::modal>

    <x-filament::modal id="viewSlotsModal" width="3xl">
        <x-slot name="heading">View Assigned Slots</x-slot>

        <div style="padding:20px;">
            @if ($viewingSlots->isEmpty())
                <p style="color:#6B7280;font-size:14px;text-align:center;padding:12px 0;">
                    No slots assigned for the selected date(s).
                </p>
            @else
                <div
                    style="overflow-x:auto;border:1px solid #E5E7EB;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <table style="width:100%;border-collapse:collapse;min-width:600px;">
                        <thead>
                            <tr style="background-color:#F9FAFB;">
                                <th
                                    style="padding:12px 16px;text-align:left;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #E5E7EB;">
                                    Date
                                </th>
                                <th
                                    style="padding:12px 16px;text-align:left;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #E5E7EB;">
                                    Time
                                </th>
                                <th
                                    style="padding:12px 16px;text-align:center;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #E5E7EB;">
                                    Status
                                </th>
                                <th
                                    style="padding:12px 16px;text-align:center;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #E5E7EB;">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedSlots = $viewingSlots->groupBy(
                                    fn($slot) => \Carbon\Carbon::parse($slot->date)->format('Y-m-d'),
                                );
                                $currentDate = null;
                                $rowspanCount = 0;
                            @endphp
                            @foreach ($groupedSlots as $dateKey => $dateSlots)
                                @php
                                    $rowspanCount = $dateSlots->count();
                                    $currentDate = $dateKey;
                                @endphp
                                @foreach ($dateSlots as $index => $slot)
                                    <tr style="border-bottom:1px solid #E5E7EB;transition:background-color 0.2s;"
                                        onmouseover="this.style.backgroundColor='#F9FAFB';"
                                        onmouseout="this.style.backgroundColor='white';">
                                        @if ($index === 0)
                                            <td style="padding:12px 16px;font-size:14px;color:#111827;white-space:nowrap;"
                                                rowspan="{{ $rowspanCount }}">
                                                {{ \Carbon\Carbon::parse($dateKey)->format('M j, Y') }}
                                            </td>
                                        @endif
                                        <td style="padding:12px 16px;font-size:14px;color:#111827;white-space:nowrap;">
                                            {{ \Carbon\Carbon::parse($slot->slot?->start_time)->format('g:i A') }} –
                                            {{ \Carbon\Carbon::parse($slot->slot?->end_time)->format('g:i A') }}
                                        </td>
                                        <td
                                            style="padding:12px 16px;text-align:center;font-size:13px;font-weight:500;white-space:nowrap;">
                                            @if ($slot->is_booked)
                                                <span
                                                    style="background-color:#FEE2E2;color:#991B1B;padding:4px 8px;border-radius:6px;font-size:12px;">Booked</span>
                                            @else
                                                <span
                                                    style="background-color:#DCFCE7;color:#166534;padding:4px 8px;border-radius:6px;font-size:12px;">Available</span>
                                            @endif
                                        </td>
                                        <td style="padding:12px 16px;text-align:center;">
                                            <x-filament::button color="danger" size="xs"
                                                wire:click="deleteSlot({{ $slot->id }})">
                                                Delete
                                            </x-filament::button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </x-filament::modal>

    <!-- Calendar Container -->
    <div
        style="background-color:white;border:1px solid #E5E7EB;border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">

        <!-- Month Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="font-size:22px;font-weight:700;color:#111827;margin:0;">{{ $currentMonth->format('F Y') }}</h2>
            <div style="display:flex;gap:8px;">
                <x-filament::button icon="heroicon-o-chevron-left" wire:click="navigateMonth('prev')" size="sm"
                    style="background-color:#F9FAFB;border:1px solid #E5E7EB;border-radius:6px;padding:6px;cursor:pointer;" />
                <x-filament::button icon="heroicon-o-chevron-right" wire:click="navigateMonth('next')" size="sm"
                    style="background-color:#F9FAFB;border:1px solid #E5E7EB;border-radius:6px;padding:6px;cursor:pointer;" />
            </div>
        </div>

        <!-- Weekday Header -->
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:16px;">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div
                    style="text-align:center;font-weight:600;font-size:13px;color:#4B5563;background-color:#F9FAFB;padding:10px;border-radius:8px;">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        <!-- Calendar Grid -->
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:8px;">
            @foreach ($this->getCalendarData() as $day)
                @php
                    $isSelected = $day['is_selected'];
                    $hasSlots = $day['slots_count'] > 0;
                    $isToday = $day['is_today'];
                    $isCurrentMonth = $day['is_current_month'];
                @endphp

                <div wire:click="toggleDateSelection('{{ $day['date_string'] }}')"
                    style="
                        min-height:110px;
                        padding:12px;
                        border:2px solid {{ $isSelected ? '#60A5FA' : '#E5E7EB' }};
                        border-radius:12px;
                        background-color: {{ !$isCurrentMonth ? '#F9FAFB' : ($isSelected ? '#DBEAFE' : ($isToday ? '#EFF6FF' : '#FFFFFF')) }};
                        box-shadow:{{ $isSelected ? '0 2px 6px rgba(37,99,235,0.2)' : 'none' }};
                        cursor:pointer;
                        transition:all 0.2s ease-in-out;
                    ">
                    <!-- Date & Count -->
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                        <span
                            style="font-size:18px;font-weight:600;color: {{ $isToday ? '#2563EB' : ($isCurrentMonth ? '#111827' : '#9CA3AF') }};">
                            {{ $day['date']->format('j') }}
                        </span>
                        @if ($hasSlots)
                            <span
                                style="font-size:11px;padding:3px 8px;border-radius:9999px;font-weight:500; background-color:{{ $day['available_slots'] > 0 ? '#DCFCE7' : '#E5E7EB' }}; color:{{ $day['available_slots'] > 0 ? '#166534' : '#374151' }};">
                                {{ $day['slots_count'] }}
                            </span>
                        @endif
                    </div>

                    <!-- Slot Info -->
                    @if ($hasSlots && $isCurrentMonth)
                        <div style="margin-top:6px;">
                            @if ($day['available_slots'] > 0)
                                <div
                                    style="font-size:12px;font-weight:500;color:#15803D;background-color:#ECFDF5;padding:4px 8px;border-radius:6px;">
                                    {{ $day['available_slots'] }} available
                                </div>
                            @else
                                <div
                                    style="font-size:12px;font-weight:500;color:#6B7280;background-color:#F3F4F6;padding:4px 8px;border-radius:6px;">
                                    Fully booked
                                </div>
                            @endif
                        </div>
                    @elseif ($isCurrentMonth && !$hasSlots)
                        <div style="margin-top:8px;">
                            <span style="font-size:12px;color:#9CA3AF;font-style:italic;">No slots</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Selected Dates Summary -->
        @if (count($selectedDates) > 0)
            <div
                style="margin-top:24px;background-color:#EFF6FF;border:2px solid #BFDBFE;border-radius:8px;padding:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;">
                    <div>
                        <h3 style="font-size:16px;font-weight:600;color:#1E3A8A;margin:0;">
                            {{ count($selectedDates) }} date(s) selected
                        </h3>
                        <p style="font-size:13px;color:#1D4ED8;margin-top:6px;">
                            @foreach (array_slice($selectedDates, 0, 3) as $date)
                                <span
                                    style="display:inline-block;background-color:#DBEAFE;color:#1E3A8A;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:500;margin-right:4px;margin-bottom:4px;">
                                    {{ \Carbon\Carbon::parse($date)->format('M j, Y') }}
                                </span>
                            @endforeach
                            @if (count($selectedDates) > 3)
                                <span style="color:#2563EB;font-weight:500;">and {{ count($selectedDates) - 3 }}
                                    more...</span>
                            @endif
                        </p>
                    </div>

                    <div style="display:flex;gap:10px;align-items:center;">
                        <x-filament::button wire:click="viewSelectedSlots" color="gray" icon="heroicon-o-eye">
                            View Slots
                        </x-filament::button>
                        <x-filament::button wire:click="deleteSelectedSlots" color="danger" icon="heroicon-o-trash">
                            Delete Slots
                        </x-filament::button>
                    </div>
                </div>
            </div>
        @else
            <div
                style="margin-top:24px;background-color:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;padding:16px;text-align:center;">
                <p style="font-size:14px;color:#6B7280;margin:0;">
                    Click on dates to select them, then click "Create Slots" to add time slots.
                </p>
            </div>
        @endif

        <!-- Legend -->
        <div
            style="margin-top:24px;display:flex;justify-content:center;align-items:center;gap:16px;flex-wrap:wrap;font-size:12px;">
            <div style="display:flex;align-items:center;gap:6px;">
                <div
                    style="width:16px;height:16px;background-color:#DCFCE7;border:1px solid #86EFAC;border-radius:4px;">
                </div>
                <span style="color:#4B5563;">Available slots</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <div
                    style="width:16px;height:16px;background-color:#DBEAFE;border:1px solid #93C5FD;border-radius:4px;">
                </div>
                <span style="color:#4B5563;">Selected date</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <div
                    style="width:16px;height:16px;background-color:#EFF6FF;border:1px solid #BFDBFE;border-radius:4px;">
                </div>
                <span style="color:#4B5563;">Today</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <div
                    style="width:16px;height:16px;background-color:#F3F4F6;border:1px solid #E5E7EB;border-radius:4px;opacity:0.6;">
                </div>
                <span style="color:#4B5563;">Other month</span>
            </div>
        </div>
    </div>
</x-filament-panels::page>
