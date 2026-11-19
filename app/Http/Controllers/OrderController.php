<?php

namespace App\Http\Controllers;

use App\Actions\Orders\FindCustomerOrder;
use App\Actions\Orders\ListCustomerOrders;
use App\Models\Order;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request, ListCustomerOrders $listOrders): Response
    {
        $orders = $listOrders->handle($request->user())->through(fn (Order $order) => [
            'reference' => $order->reference,
            'status' => $order->status,
            'total' => $order->total,
            'placed_at' => $order->created_at->format('M j, Y'),
            'items_count' => $order->items->sum('quantity'),
        ]);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, string $reference, FindCustomerOrder $findOrder): Response
    {
        $order = $findOrder->handle($request->user(), $reference);

        return Inertia::render('Orders/Show', [
            'order' => [
                'reference' => $order->reference,
                'status' => $order->status,
                'total' => $order->total,
                'placed_at' => $order->created_at->format('M j, Y g:i A'),
                'paid_at' => $this->paidAt($order)?->format('M j, Y g:i A'),
                'items' => $order->items->map(fn ($item) => [
                    'title' => $item->title,
                    'label' => $item->variant_label,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'line_total' => round((float) $item->price * $item->quantity, 2),
                ])->values(),
                'refunded' => $order->refundedTotal(),
            ],
        ]);
    }

    private function paidAt(Order $order): ?CarbonInterface
    {
        if (! in_array($order->status, Order::PAID_STATUSES, true)) {
            return null;
        }

        return $order->paid_at ?? $order->updated_at;
    }
}
