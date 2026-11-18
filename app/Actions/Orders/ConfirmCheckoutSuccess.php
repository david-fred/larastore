<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\User;

class ConfirmCheckoutSuccess
{
    public function handle(User $user, ?string $sessionId): ?Order
    {
        if (! $sessionId) {
            return null;
        }

        $order = Order::where('stripe_session_id', $sessionId)
            ->where('user_id', $user->id)
            ->first();

        if ($order) {
            session()->forget('cart');
        }

        return $order;
    }
}
