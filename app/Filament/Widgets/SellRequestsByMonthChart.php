<?php

namespace App\Filament\Widgets;

use App\Models\SellingRequest;
use Filament\Widgets\ChartWidget;

class SellRequestsByMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Sell Requests by Month';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();

        $data = SellingRequest::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
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
                    'label' => 'Sell Requests',
                    'data' => array_values($allMonths),
                    'borderColor' => 'rgb(249, 115, 22)',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.1)',
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
