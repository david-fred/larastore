<?php

namespace App\Actions\Cart;

use App\Models\ProductVariant;

class GetCartItems
{
    public function handle(): array
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return ['items' => [], 'subtotal' => 0];
        }

        $variants = ProductVariant::whereIn('id', array_keys($cart))
            ->whereHas('product', fn ($query) => $query->where('active', true))
            ->with(['product.images', 'options.type'])
            ->get();

        // Drop entries for variants that were deleted or whose product is inactive
        session(['cart' => array_intersect_key($cart, $variants->pluck('id')->flip()->all())]);

        $items = $variants->map(function (ProductVariant $variant) use ($cart) {
            $quantity = $cart[$variant->id];
            $price = (float) $variant->price;

            return [
                'variant_id' => $variant->id,
                'product_slug' => $variant->product->slug,
                'title' => $variant->product->title,
                'label' => $variant->label,
                'price' => $price,
                'quantity' => $quantity,
                'stock' => $variant->quantity,
                'image' => $variant->product->images->first()?->url(),
                'line_total' => round($price * $quantity, 2),
            ];
        })->values();

        return [
            'items' => $items,
            'subtotal' => round($items->sum('line_total'), 2),
        ];
    }
}
