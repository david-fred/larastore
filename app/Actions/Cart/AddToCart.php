<?php

namespace App\Actions\Cart;

use App\Models\Product;
use Illuminate\Validation\ValidationException;

class AddToCart
{
    public function handle(Product $product, int $quantity = 1): void
    {
        $cart = session('cart', []);
        $newQuantity = ($cart[$product->id] ?? 0) + $quantity;

        if ($newQuantity > $product->quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$product->quantity} of {$product->title} are in stock.",
            ]);
        }

        $cart[$product->id] = $newQuantity;

        session(['cart' => $cart]);
    }
}
