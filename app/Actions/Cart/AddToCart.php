<?php

namespace App\Actions\Cart;


use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

class AddToCart
{
    public function handle(ProductVariant $variant, int $quantity = 1): void
    {
        $cart = session('cart', []);
        $newQuantity = ($cart[$variant->id] ?? 0) + $quantity;

        if ($newQuantity > $variant->quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$variant->quantity} of {$variant->display_name} are in stock.",
            ]);
        }

        $cart[$variant->id] = $newQuantity;

        session(['cart' => $cart]);
    }
}
