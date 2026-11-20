<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_apply_to_become_a_vendor(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('vendor.apply.store'), [
                'store_name' => 'Fred Gadgets',
                'description' => 'Phones and accessories',
            ])
            ->assertRedirect(route('vendor.apply'));

        $this->assertDatabaseHas('vendors', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_apply_twice(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('vendor.apply.store'), [
            'store_name' => 'Fred Gadgets',
        ]);

        $this->actingAs($user)
            ->post(route('vendor.apply.store'), [
                'store_name' => 'Another Store',
            ])
            ->assertSessionHasErrors('vendor');
    }
}
