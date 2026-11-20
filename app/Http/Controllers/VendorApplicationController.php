<?php

namespace App\Http\Controllers;

use App\Actions\Vendors\ApplyForVendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VendorApplicationController extends Controller
{
    public function create(Request $request): Response
    {
        $vendor = $request->user()->vendor;

        return Inertia::render('Vendor/Apply', [
            'vendor' => $vendor ? [
                'store_name' => $vendor->store_name,
                'status' => $vendor->status,
            ] : null,
        ]);
    }

    public function store(Request $request, ApplyForVendor $applyForVendor): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:100', 'unique:vendors,store_name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $applyForVendor->handle($request->user(), $validated);

        return redirect()->route('vendor.apply');
    }
}
