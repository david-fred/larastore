<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class StoreProduct
{
    public function handle(array $data): Product
    {
        $images = Arr::pull($data, 'images', []);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $product = Product::create($data);

        app(AttachProductImages::class)->handle($product, $images);

        return $product;
    }
}
