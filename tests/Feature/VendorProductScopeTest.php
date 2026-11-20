<?php

namespace Tests\Feature;

use App\Actions\Vendors\ApproveVendor;
use App\Models\Category;
use App\Models\Department;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorProductScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'vendor']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    private function approvedSeller(string $name): User
    {
        $user = User::factory()->create();

        app(ApproveVendor::class)->handle(Vendor::create([
            'user_id' => $user->id,
            'store_name' => $name,
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(4)),
        ]));

        return $user->fresh();
    }

    private function productFor(User $seller): Product
    {
        $department = Department::create([
            'name' => 'Tech',
            'slug' => 'tech-' . Str::random(4),
            'active' => true,
        ]);

        $category = Category::create([
            'department_id' => $department->id,
            'name' => 'Phones',
            'slug' => 'phones-' . Str::random(4),
            'active' => true,
        ]);

        return Product::create([
            'vendor_id' => $seller->vendor->id,
            'category_id' => $category->id,
            'title' => 'Phone',
            'slug' => 'phone-' . Str::random(6),
            'price' => 100,
            'quantity' => 5,
            'active' => true,
        ]);
    }

    public function test_seller_cannot_open_another_sellers_product(): void
    {
        $owner = $this->approvedSeller('Owner Store');
        $other = $this->approvedSeller('Other Store');
        $product = $this->productFor($owner);

        $this->actingAs($other)
            ->get("/vendor/products/{$product->id}/edit")
            ->assertNotFound();
    }

    public function test_customer_cannot_open_the_seller_panel(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/vendor')
            ->assertForbidden();
    }
}
