<?php

namespace App\Filament\Resources\DiscountBannerResource\Pages;

use App\Filament\Resources\DiscountBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiscountBanners extends ListRecords
{
    protected static string $resource = DiscountBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
