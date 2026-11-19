<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ListCustomerOrders
{
    public function handle(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('user_id', $user->id)
            ->where('status', 'paid')
            ->with('items')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
