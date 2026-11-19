<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class RefundOrder
{
    public function handle(Order $order, float $amount, string $reason, User $admin): Refund
    {
        // Step 1: reserve the amount in the database, so overlapping refunds can't exceed the total
        $refund = DB::transaction(function () use ($order, $amount, $reason, $admin) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if (! $order->canBeRefunded()) {
                throw ValidationException::withMessages([
                    'amount' => 'This order cannot be refunded.',
                ]);
            }

            if ($amount <= 0 || $amount > $order->refundableAmount()) {
                throw ValidationException::withMessages([
                    'amount' => 'Refund must be more than $0 and no more than $' . number_format($order->refundableAmount(), 2) . '.',
                ]);
            }

            return $order->refunds()->create([
                'amount' => $amount,
                'reason' => $reason,
                'status' => 'pending',
                'created_by' => $admin->id,
            ]);
        });

        // Step 2: call Stripe outside the transaction, so the lock isn't held over the network call
        try {
            $stripeRefund = (new StripeClient(config('services.stripe.secret')))->refunds->create(
                [
                    'payment_intent' => $order->stripe_payment_intent_id,
                    'amount' => (int) round($amount * 100),
                    'reason' => 'requested_by_customer',
                    'metadata' => [
                        'order_reference' => $order->reference,
                        'refund_id' => $refund->id,
                    ],
                ],
                // Retrying this request can't create a second refund
                ['idempotency_key' => 'refund-' . $refund->id],
            );
        } catch (ApiErrorException $e) {
            Log::error('Stripe refund failed', ['refund_id' => $refund->id, 'message' => $e->getMessage()]);

            // Releases the reserved amount, since the refund didn't happen
            $refund->update(['status' => 'failed']);

            throw ValidationException::withMessages([
                'amount' => 'Stripe rejected the refund. Check the logs for details.',
            ]);
        }

        // Step 3: record the result and update the order status
        DB::transaction(function () use ($order, $refund, $stripeRefund) {
            $refund->update([
                'status' => 'succeeded',
                'stripe_refund_id' => $stripeRefund->id,
            ]);

            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            $order->update([
                'status' => $order->refundableAmount() <= 0 ? 'refunded' : 'partially_refunded',
            ]);
        });

        return $refund->fresh();
    }
}
