<?php

namespace App\Actions\Cart;

class RemoveFromCart
{
    public function handle(int $productId): void
    {
        $cart = session('cart', []);

        unset($cart[$productId]);

        session(['cart' => $cart]);
    }
}
