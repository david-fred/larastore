<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Actions\Products\UpdateProduct;
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
        $data['gallery'] = $this->record->images()
            ->whereNull('product_variation_option_id')
            ->get()
            ->map(fn ($image) => ['path' => $image->path])
            ->all();

        $data['variation_types'] = $this->record->variationTypes()
            ->with('options.images')
            ->get()
            ->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->name,
                'options' => $type->options->map(fn ($option) => [
                    'id' => $option->id,
                    'name' => $option->name,
                    'images' => $option->images->pluck('path')->all(),
                ])->all(),
            ])
            ->all();

        return $data;
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
