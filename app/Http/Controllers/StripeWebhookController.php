<?php

namespace App\Http\Controllers;

use App\Actions\Orders\HandleStripeCheckoutCompleted;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, HandleStripeCheckoutCompleted $handleCompleted): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                config('services.stripe.webhook_secret'),
            );
        } catch (\UnexpectedValueException | SignatureVerificationException) {
            // Not from Stripe, or the payload was tampered with
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $handleCompleted->handle($event->data->object->toArray());
        }

        return response('OK', 200);
    }
}
