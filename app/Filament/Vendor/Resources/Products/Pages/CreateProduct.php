<?php

namespace App\Filament\Vendor\Resources\Products\Pages;

use App\Actions\Products\StoreProduct;
use App\Filament\Vendor\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // The seller is always the logged-in vendor, never a value from the form
        $data['vendor_id'] = auth()->user()->vendor->id;

        return app(StoreProduct::class)->handle($data);
    }
}
