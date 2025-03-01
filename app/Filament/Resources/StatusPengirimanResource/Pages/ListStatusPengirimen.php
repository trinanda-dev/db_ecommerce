<?php

namespace App\Filament\Resources\StatusPengirimanResource\Pages;

use App\Filament\Resources\StatusPengirimanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatusPengirimen extends ListRecords
{
    protected static string $resource = StatusPengirimanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
