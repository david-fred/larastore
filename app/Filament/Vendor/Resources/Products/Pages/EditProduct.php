<?php

namespace App\Filament\Vendor\Resources\Products\Pages;

use App\Actions\Products\UpdateProduct;
use App\Filament\Vendor\Resources\Products\ProductResource;
use App\Models\Product;
use App\Support\ProductFormData;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return ProductFormData::fill($this->record, $data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        assert($record instanceof Product);

        return app(UpdateProduct::class)->handle($record, $data);
    }
}
