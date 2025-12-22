<?php

namespace App\Filament\Resources\SellingRequestResource\Pages;

use App\Filament\Resources\SellingRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSellingRequest extends EditRecord
{
    protected static string $resource = SellingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
