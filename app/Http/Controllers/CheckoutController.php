<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderFromCart;
use App\Actions\Orders\CreateStripeCheckout;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function store(Request $request, CreateOrderFromCart $createOrder, CreateStripeCheckout $checkout)
    {
        $order = $createOrder->handle($request->user());

        $url = $checkout->handle($order);

        // Stripe is an external site, so Inertia needs a full redirect
        return Inertia::location($url);
    }
}
