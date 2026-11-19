<?php

namespace App\Actions\Orders;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HandleStripeCheckoutCompleted
{
    public function handle(array $session): void
    {
        $order = Order::where('stripe_session_id', $session['id'] ?? null)->first();

        if (! $order) {
            return; // Not one of our orders, e.g. a test event from the Stripe CLI
        }

        // Returns the order only when this call is the one that marked it paid
        $justPaid = DB::transaction(function () use ($order) {
            // Webhooks can be retried, so only process an order once
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($order->status === 'paid') {
                return null;
            }

            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
                'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
            ]);

            foreach ($order->items as $item) {
                if (! $item->product_variant_id) {
                    continue;
                }

                $variant = ProductVariant::whereKey($item->product_variant_id)->lockForUpdate()->first();

                if ($variant) {
                    $variant->decrement('quantity', min($item->quantity, $variant->quantity));
                }
            }

            return $order;
        });

        // Sent after the commit, so the queued job always sees the paid order
        if ($justPaid) {
            Mail::to($justPaid->user)->queue(new OrderConfirmation($justPaid));
        }
    }
}
