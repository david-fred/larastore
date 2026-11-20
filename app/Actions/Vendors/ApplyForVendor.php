<?php

namespace App\Actions\Vendors;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApplyForVendor
{
    public function handle(User $user, array $data): Vendor
    {
        if ($user->vendor()->exists()) {
            throw ValidationException::withMessages([
                'vendor' => 'You have already applied to sell on Larastore.',
            ]);
        }

        return Vendor::create([
            'user_id' => $user->id,
            'store_name' => trim($data['store_name']),
            'slug' => Str::slug($data['store_name']) . '-' . Str::lower(Str::random(4)),
            'description' => $data['description'] ?? null,
        ]);
    }
}
