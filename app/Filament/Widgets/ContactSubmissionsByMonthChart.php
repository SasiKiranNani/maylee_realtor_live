<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use Filament\Widgets\ChartWidget;

class ContactSubmissionsByMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Contact Submissions by Month';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();

        $data = ContactSubmission::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
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
                    'label' => 'Contact Submissions',
                    'data' => array_values($allMonths),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
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
