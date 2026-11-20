<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    // Admins can do anything. Everyone else goes through the checks below.
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $this->isApprovedSeller($user);
    }

    public function create(User $user): bool
    {
        return $this->isApprovedSeller($user);
    }

    public function view(User $user, Product $product): bool
    {
        return $this->owns($user, $product);
    }

    public function update(User $user, Product $product): bool
    {
        return $this->owns($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->owns($user, $product);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isApprovedSeller($user);
    }

    private function isApprovedSeller(User $user): bool
    {
        return $user->vendor?->isApproved() ?? false;
    }

    private function owns(User $user, Product $product): bool
    {
        return $product->vendor_id !== null
            && $product->vendor_id === $user->vendor?->id;
    }
}
