<?php

namespace App\Actions\Products;

use App\Models\ProductVariant;

class FindActiveVariant
{
    public function handle(int $variantId): ProductVariant
    {
        return ProductVariant::whereKey($variantId)
            ->whereHas('product', fn ($query) => $query->where('active', true))
            ->with('product')
            ->firstOrFail();
    }
}
