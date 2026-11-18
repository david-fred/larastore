<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\ProductVariant;
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

        return DB::transaction(function () use ($user, $cart) {
            $variants = ProductVariant::whereIn('id', array_keys($cart))
                ->whereHas('product', fn ($query) => $query->where('active', true))
                ->with('product')
                ->lockForUpdate()
                ->get();

            $order = Order::create([
                'user_id' => $user->id,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($variants as $variant) {
                $quantity = $cart[$variant->id];

                if ($quantity > $variant->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Only {$variant->quantity} of {$variant->display_name} in stock.",
                    ]);
                }

                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'title' => $variant->product->title,
                    'variant_label' => $variant->label,
                    'price' => $variant->price,
                    'quantity' => $quantity,
                ]);

                $total += (float) $variant->price * $quantity;
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
