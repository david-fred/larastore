<?php

namespace App\Actions\Cart;

use App\Models\Product;

class GetCartItems
{
    public function handle(): array
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return ['items' => [], 'subtotal' => 0];
        }

        $items = Product::whereIn('id', array_keys($cart))
            ->where('active', true)
            ->with('images')
            ->get()
            ->map(function (Product $product) use ($cart) {
                $quantity = $cart[$product->id];
                $price = (float) $product->price;

                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'price' => $price,
                    'quantity' => $quantity,
                    'stock' => $product->quantity,
                    'image' => $product->images->first()?->url(),
                    'line_total' => round($price * $quantity, 2),
                ];
            })
            ->values();

        return [
            'items' => $items,
            'subtotal' => round($items->sum('line_total'), 2),
        ];
    }
}
