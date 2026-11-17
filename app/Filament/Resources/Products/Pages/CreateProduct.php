<?php

namespace App\Filament\Resources\Products\Pages;

use App\Actions\Products\StoreProduct;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var StoreProduct $action */
        $action = app(StoreProduct::class);

        return $action->handle($data);
    }
}
