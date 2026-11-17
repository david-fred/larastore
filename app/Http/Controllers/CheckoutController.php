<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderFromCart;
use App\Actions\Orders\CreateStripeCheckout;
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

    public function success(Request $request): Response
    {
        return Inertia::render('Checkout/Success', [
            'sessionId' => $request->query('session_id'),
        ]);
    }
}
