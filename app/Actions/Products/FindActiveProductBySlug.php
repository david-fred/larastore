<?php

namespace App\Actions\Products;

use App\Models\Product;

class FindActiveProductBySlug
{
    public function handle(string $slug): Product
    {
        return Product::where('slug', $slug)
            ->where('active', true)
            ->with(['images', 'category'])
            ->firstOrFail();
    }
}
