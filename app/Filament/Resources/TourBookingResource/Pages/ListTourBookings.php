<?php
namespace App\Filament\Resources\TourBookingResource\Pages;

use App\Filament\Resources\TourBookingResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTourBookings extends ListRecords
{
    protected static string $resource = TourBookingResource::class;

    public function getTabs(): array
    {
        return [
            'today'     => Tab::make('Today Bookings')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', Carbon::today()))
                ->badge(fn() => \App\Models\TourBooking::whereDate('date', Carbon::today())->count()),

            'upcoming'  => Tab::make('Upcoming Bookings')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date', '>', Carbon::today()))
                ->badge(fn() => \App\Models\TourBooking::where('date', '>', Carbon::today())->count()),

            'completed' => Tab::make('Completed Bookings')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date', '<', Carbon::today()))
                ->badge(fn() => \App\Models\TourBooking::where('date', '<', Carbon::today())->count()),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
