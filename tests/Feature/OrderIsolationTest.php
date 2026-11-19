<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function paidOrderFor(User $user): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'reference' => 'LS-' . strtoupper(Str::random(8)),
            'status' => 'paid',
            'total' => 10,
        ]);
    }

    public function test_customer_can_open_their_own_order(): void
    {
        $owner = User::factory()->create();
        $order = $this->paidOrderFor($owner);

        $this->actingAs($owner)
            ->get(route('orders.show', $order->reference))
            ->assertOk();
    }

    public function test_customer_cannot_open_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->paidOrderFor($owner);

        $this->actingAs($other)
            ->get(route('orders.show', $order->reference))
            ->assertNotFound();
    }

    public function test_customer_cannot_open_the_admin_panel(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/admin/orders')
            ->assertForbidden();
    }
}
