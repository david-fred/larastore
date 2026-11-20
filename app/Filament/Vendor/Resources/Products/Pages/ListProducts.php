<?php

namespace App\Filament\Vendor\Resources\Products\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Vendor\Resources\Products\ProductResource;

use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
