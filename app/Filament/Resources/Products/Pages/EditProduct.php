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
            ->get()
            ->map(fn ($image) => [
                'path' => $image->path,
                'option_key' => $image->product_variation_option_id
                    ? 'id:' . $image->product_variation_option_id
                    : null,
            ])
            ->all();

        $data['variation_types'] = $this->record->variationTypes()
            ->with('options')
            ->get()
            ->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->name,
                'affects_images' => (bool) $type->affects_images,
                'options' => $type->options->map(fn ($option) => [
                    'id' => $option->id,
                    'name' => $option->name,
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
