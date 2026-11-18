<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Models\ProductVariationOption;
use App\Models\ProductVariationType;

class SyncProductVariationTypes
{
    public function handle(Product $product, array $types): void
    {
        $keptTypeIds = [];

        foreach (array_values($types) as $typeOrder => $typeData) {
            $type = $this->saveType($product, $typeData, $typeOrder);
            $keptTypeIds[] = $type->id;

            $keptOptionIds = [];

            foreach (array_values($typeData['options'] ?? []) as $optionOrder => $optionData) {
                $keptOptionIds[] = $this->saveOption($type, $optionData, $optionOrder)->id;
            }

            $type->options()->whereNotIn('id', $keptOptionIds)->get()->each->delete();
        }

        $product->variationTypes()->whereNotIn('id', $keptTypeIds)->get()->each->delete();
    }

    private function saveType(Product $product, array $data, int $order): ProductVariationType
    {
        $attributes = [
            'name' => trim($data['name']),
            'sort_order' => $order,
            'affects_images' => (bool) ($data['affects_images'] ?? false),
        ];

        $existing = ! empty($data['id'])
            ? $product->variationTypes()->whereKey($data['id'])->first()
            : null;

        if ($existing) {
            $existing->update($attributes);

            return $existing;
        }

        return $product->variationTypes()->create($attributes);
    }

    private function saveOption(ProductVariationType $type, array $data, int $order): ProductVariationOption
    {
        $attributes = ['name' => trim($data['name']), 'sort_order' => $order];

        $existing = ! empty($data['id'])
            ? $type->options()->whereKey($data['id'])->first()
            : null;

        if ($existing) {
            $existing->update($attributes);

            return $existing;
        }

        return $type->options()->create($attributes);
    }
}
