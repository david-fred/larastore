<?php

namespace App\Support;

use App\Models\Product;

class ProductFormData
{
    // Fills the product form with the saved gallery and variation types
    public static function fill(Product $product, array $data): array
    {
        $data['gallery'] = $product->images()
            ->whereNull('product_variation_option_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($image) => ['path' => $image->path])
            ->all();

        $data['variation_types'] = $product->variationTypes()
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
}
