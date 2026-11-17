<?php

namespace App\Actions\Products;

use App\Models\Product;

class AttachProductImages
{
    public function handle(Product $product, array $paths): void
    {
        $start = $product->images()->count();

        foreach (array_values($paths) as $offset => $path) {
            $product->images()->create([
                'path' => $path,
                'sort_order' => $start + $offset,
            ]);
        }
    }
}
