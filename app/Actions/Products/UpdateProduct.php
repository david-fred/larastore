<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UpdateProduct
{
    public function handle(Product $product, array $data): Product
    {
        $gallery = Arr::pull($data, 'gallery', []);
        $variationTypes = Arr::pull($data, 'variation_types', []);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $product->update($data);

        app(SyncProductVariationTypes::class)->handle($product, $variationTypes);
        app(SyncProductImages::class)->handle($product, $gallery);
        app(GenerateProductVariants::class)->handle($product);

        return $product;
    }
}
