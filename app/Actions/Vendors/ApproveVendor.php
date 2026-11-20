<?php

namespace App\Actions\Vendors;

use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class ApproveVendor
{
    public function handle(Vendor $vendor): Vendor
    {
        DB::transaction(function () use ($vendor) {
            $vendor->update([
                'status' => Vendor::STATUS_APPROVED,
                'approved_at' => now(),
            ]);

            $vendor->user->assignRole('vendor');
        });

        return $vendor;
    }
}
