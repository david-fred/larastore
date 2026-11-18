<?php

namespace App\Actions\Cart;

use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

class UpdateCartItem
{
    public function handle(ProductVariant $variant, int $quantity): void
    {
        $cart = session('cart', []);

        if ($quantity === 0) {
            unset($cart[$variant->id]);
            session(['cart' => $cart]);

            return;
        }

        if ($quantity > $variant->quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$variant->quantity} of {$variant->display_name} are in stock.",
            ]);
        }

        $cart[$variant->id] = $quantity;

        session(['cart' => $cart]);
    }
}
