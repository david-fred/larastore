<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Str;

class StoreProduct
{
    public function handle(array $data): Product
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        return Product::create($data);
    }
}
