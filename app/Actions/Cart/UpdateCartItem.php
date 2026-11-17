<?php

namespace App\Actions\Cart;

use App\Models\Product;
use Illuminate\Validation\ValidationException;

class UpdateCartItem
{
    public function handle(Product $product, int $quantity): void
    {
        $cart = session('cart', []);

        if ($quantity === 0) {
            unset($cart[$product->id]);
            session(['cart' => $cart]);

            return;
        }

        if ($quantity > $product->quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$product->quantity} of {$product->title} are in stock.",
            ]);
        }

        $cart[$product->id] = $quantity;

        session(['cart' => $cart]);
    }
}
