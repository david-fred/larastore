<?php

namespace App\Actions\Cart;

class RemoveFromCart
{
    public function handle(int $variantId): void
    {
        $cart = session('cart', []);

        unset($cart[$variantId]);

        session(['cart' => $cart]);
    }
}
