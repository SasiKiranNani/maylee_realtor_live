<?php

namespace App\Filament\Widgets;

use App\Models\TourBooking;
use Filament\Widgets\ChartWidget;

class TourBookingsByMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Tour Bookings by Month';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();

        $data = TourBooking::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Generate all months in range
        $allMonths = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $allMonths[$current->format('Y-m')] = 0;
            $current->addMonth();
        }

        // Fill in actual data
        foreach ($data as $row) {
            $allMonths[$row->month] = $row->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tour Bookings',
                    'data' => array_values($allMonths),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => array_keys($allMonths),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
