<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderFromCart
{
    public function handle(User $user): Order
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $products = Product::whereIn('id', array_keys($cart))
            ->where('active', true)
            ->get();

        return DB::transaction(function () use ($user, $cart, $products) {
            $order = Order::create([
                'user_id' => $user->id,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($products as $product) {
                $quantity = $cart[$product->id];

                if ($quantity > $product->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Only {$product->quantity} of {$product->title} are in stock.",
                    ]);
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'price' => $product->price,
                    'quantity' => $quantity,
                ]);

                $total += (float) $product->price * $quantity;
            }

            if ($order->items()->count() === 0) {
                throw ValidationException::withMessages([
                    'cart' => 'None of the items in your cart are available.',
                ]);
            }

            $order->update(['total' => round($total, 2)]);

            return $order->load('items');
        });
    }
}
