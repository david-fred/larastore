<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\ProductVariant;
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
                if (! $item->product_variant_id) {
                    continue;
                }

                $variant = ProductVariant::whereKey($item->product_variant_id)->lockForUpdate()->first();

                if ($variant) {
                    $variant->decrement('quantity', min($item->quantity, $variant->quantity));
                }
            }
        });
    }
}
