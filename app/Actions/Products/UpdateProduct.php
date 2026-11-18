<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UpdateProduct
{
    public function handle(Product $product, array $data): Product
    {
        $images = Arr::pull($data, 'images', []);
        $variationTypes = Arr::pull($data, 'variation_types', []);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $product->update($data);

        app(SyncProductImages::class)->handle($product, $images);
        app(SyncProductVariationTypes::class)->handle($product, $variationTypes);

        return $product;
    }
}
