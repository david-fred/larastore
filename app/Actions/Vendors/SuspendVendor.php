<?php

namespace App\Actions\Vendors;

use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class SuspendVendor
{
    public function handle(Vendor $vendor): Vendor
    {
        DB::transaction(function () use ($vendor) {
            $vendor->update(['status' => Vendor::STATUS_SUSPENDED]);

            $vendor->user->removeRole('vendor');
        });

        return $vendor;
    }
}
