<?php

namespace App\Actions\Products;

use Illuminate\Database\Eloquent\Collection;

class BulkUpdateVariants
{
    public function handle(Collection $variants, ?float $price, ?string $stockMode, ?int $stock): int
    {
        $updated = 0;

        foreach ($variants as $variant) {
            $attributes = [];

            if ($price !== null) {
                $attributes['price'] = $price;
            }

            if ($stock !== null && $stockMode === 'set') {
                $attributes['quantity'] = max(0, $stock);
            }

            if ($stock !== null && $stockMode === 'add') {
                $attributes['quantity'] = max(0, $variant->quantity + $stock);
            }

            if ($attributes) {
                $variant->update($attributes);
                $updated++;
            }
        }

        return $updated;
    }
}
