<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Actions\Products\UpdateProduct;
use App\Support\ProductFormData;
use App\Models\Product;
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

        /** @var UpdateProduct $action */
        $action = app(UpdateProduct::class);

        return $action->handle($record, $data);
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }

}
