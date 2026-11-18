<?php

namespace App\Actions\Products;

use App\Models\Product;

class SyncProductVariationTypes
{
    public function handle(Product $product, array $types): void
    {
        $keptTypeIds = [];

        foreach (array_values($types) as $typeOrder => $typeData) {
            $type = $product->variationTypes()->updateOrCreate(
                ['id' => $typeData['id'] ?? null],
                ['name' => $typeData['name'], 'sort_order' => $typeOrder],
            );

            $keptOptionIds = [];

            foreach (array_values($typeData['options'] ?? []) as $optionOrder => $optionData) {
                $option = $type->options()->updateOrCreate(
                    ['id' => $optionData['id'] ?? null],
                    ['name' => $optionData['name'], 'sort_order' => $optionOrder],
                );

                $keptOptionIds[] = $option->id;
            }

            $type->options()->whereNotIn('id', $keptOptionIds)->get()->each->delete();

            $keptTypeIds[] = $type->id;
        }

        $product->variationTypes()->whereNotIn('id', $keptTypeIds)->get()->each->delete();
    }
}
