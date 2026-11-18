<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CreateStripeCheckout
{
    public function handle(Order $order): string
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $lineItems = $order->items
            ->map(function (OrderItem $item) {
                $name = $item->variant_label && $item->variant_label !== 'Default'
                    ? "{$item->title} ({$item->variant_label})"
                    : $item->title;

                return [
                    'quantity' => $item->quantity,
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => (int) round((float) $item->price * 100),
                        'product_data' => [
                            'name' => $name,
                        ],
                    ],
                ];
            })
            ->values()
            ->all();

        try {
            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $order->user->email,
                'line_items' => $lineItems,
                'metadata' => [
                    'order_id' => $order->id,
                ],
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cart.index'),
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session failed', ['message' => $e->getMessage()]);

            $order->update(['status' => 'failed']);

            throw ValidationException::withMessages([
                'cart' => 'Payment could not be started. Please try again.',
            ]);
        }

        $order->update(['stripe_session_id' => $session->id]);

        return $session->url;
    }
}
