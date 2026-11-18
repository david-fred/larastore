<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderFromCart;
use App\Actions\Orders\CreateStripeCheckout;
use App\Actions\Orders\ConfirmCheckoutSuccess;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function store(Request $request, CreateOrderFromCart $createOrder, CreateStripeCheckout $checkout)
    {
        $order = $createOrder->handle($request->user());

        $url = $checkout->handle($order);

        // Stripe is an external site, so Inertia needs a full redirect
        return Inertia::location($url);
    }

    public function success(Request $request, ConfirmCheckoutSuccess $confirm): Response
    {
        $order = $confirm->handle($request->user(), $request->query('session_id'));

        return Inertia::render('Checkout/Success', [
            'order' => $order ? [
                'id' => $order->id,
                'status' => $order->status,
                'total' => $order->total,
            ] : null,
        ]);
    }
}
