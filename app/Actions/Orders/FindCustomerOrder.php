<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\User;

class FindCustomerOrder
{
    public function handle(User $user, string $reference): Order
    {
        // Starts from this user's orders, so another customer's order can never match
        return $user->orders()
            ->where('reference', $reference)
            ->whereIn('status', Order::PAID_STATUSES)
            ->with('items')
            ->firstOrFail();
    }
}
