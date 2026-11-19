<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Validation\ValidationException;

class MarkOrderShipped
{
    public function handle(Order $order): Order
    {
        if (! in_array($order->status, ['paid', 'partially_refunded'], true) || $order->shipped_at !== null) {
            throw ValidationException::withMessages([
                'status' => 'Only paid orders that have not shipped can be marked as shipped.',
            ]);
        }

        $order->update(['shipped_at' => now()]);

        return $order;
    }
}
