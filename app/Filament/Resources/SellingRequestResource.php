<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SellingRequestResource\Pages;
use App\Models\SellingRequest;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class SellingRequestResource extends Resource
{
    protected static ?string $model = SellingRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'Sell Requests';
    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // First Image
                ImageColumn::make('images.image_path')
                    ->label('Photo')
                    ->size(60)
                    ->rounded()
                    ->defaultImageUrl(asset('images/no-image.jpg')) // optional fallback
                    ->getStateUsing(fn ($record) => $record->images->first()?->image_path
                        ? asset('storage/' . $record->images->first()->image_path)
                        : null),

                // Seller Name
                TextColumn::make('sell_property_user_name')
                    ->label('Seller Name')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                // Email
                TextColumn::make('sell_property_user_email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email copied!'),

                // Phone
                TextColumn::make('sell_property_user_phone')
                    ->label('Phone')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->copyable(),

                // Address
                TextColumn::make('sell_property_address')
                    ->label('Property Address')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->sell_property_address),

                // Property Type
                TextColumn::make('sell_property_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Detached Home' => 'success',
                        'Condos' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // This opens the beautiful modal
                Action::make('view')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => $record->sell_property_user_name . "'s Sell Request")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn ($record) => view('filament.modals.selling-request-details', [
                        'record' => $record
                    ]))
                    ->modalWidth('6xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('30s'); // optional: auto-refresh
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellingRequests::route('/'),
        ];
    }
}
