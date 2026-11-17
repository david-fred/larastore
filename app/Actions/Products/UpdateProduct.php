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

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $product->update($data);

        app(AttachProductImages::class)->handle($product, $images);

        return $product;
    }
}
