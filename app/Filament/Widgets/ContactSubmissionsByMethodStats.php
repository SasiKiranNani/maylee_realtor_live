<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactSubmissionsByMethodStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $total = ContactSubmission::count();
        $emailCount = ContactSubmission::whereNotNull('email')->count();
        $phoneCount = ContactSubmission::whereNotNull('phone')->count();
        $bothCount = ContactSubmission::whereNotNull('email')
            ->whereNotNull('phone')
            ->count();

        // Calculate percentages
        $emailPercentage = $total > 0 ? round(($emailCount / $total) * 100, 1) : 0;
        $phonePercentage = $total > 0 ? round(($phoneCount / $total) * 100, 1) : 0;

        return [
            Stat::make('Total Contact Submissions', $total)
                ->description('All time submissions')
                ->descriptionIcon('heroicon-m-inbox-stack')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3, $total]),

            Stat::make('Email Submissions', $emailCount)
                ->description("{$emailPercentage}% of total submissions")
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success')
                ->chart(array_fill(0, 7, rand(1, $emailCount))),

            Stat::make('Phone Submissions', $phoneCount)
                ->description("{$phonePercentage}% of total submissions")
                ->descriptionIcon('heroicon-m-phone')
                ->color('warning')
                ->chart(array_fill(0, 7, rand(1, $phoneCount))),

            Stat::make('Both Email & Phone', $bothCount)
                ->description('Submissions with both contacts')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info')
                ->chart(array_fill(0, 7, rand(0, $bothCount))),
        ];
    }
}
