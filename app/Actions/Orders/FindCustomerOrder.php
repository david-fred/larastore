<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\User;

class FindCustomerOrder
{
    public function handle(User $user, string $reference): Order
    {
        // Scoped to the logged-in user, so other customers' orders return a 404
        return Order::where('reference', $reference)
            ->where('user_id', $user->id)
            ->with('items')
            ->firstOrFail();
    }
}
