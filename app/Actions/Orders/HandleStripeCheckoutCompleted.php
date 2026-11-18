<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HandleStripeCheckoutCompleted
{
    public function handle(array $session): void
    {
        $order = Order::where('stripe_session_id', $session['id'] ?? null)->first();

        if (! $order) {
            return; // Not one of our orders, e.g. a test event from the Stripe CLI
        }

        DB::transaction(function () use ($order) {
            // Webhooks can be retried, so only process an order once
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($order->status === 'paid') {
                return;
            }

            $order->update(['status' => 'paid']);

            foreach ($order->items as $item) {
                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

                if ($product) {
                    $product->decrement('quantity', min($item->quantity, $product->quantity));
                }
            }
        });
    }
}
