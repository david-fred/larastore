<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddToCart;
use App\Actions\Cart\GetCartItems;
use App\Actions\Cart\RemoveFromCart;
use App\Actions\Cart\UpdateCartItem;
use App\Actions\Products\FindActiveVariant;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(GetCartItems $getCartItems): Response
    {
        return Inertia::render('Cart/Index', $getCartItems->handle());
    }

    public function store(Request $request, FindActiveVariant $findVariant, AddToCart $addToCart): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $variant = $findVariant->handle($validated['variant_id']);

        $addToCart->handle($variant, $validated['quantity'] ?? 1);

        return back();
    }

    public function update(Request $request, ProductVariant $variant, UpdateCartItem $updateCartItem): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $updateCartItem->handle($variant, $validated['quantity']);

        return back();
    }

    public function destroy(ProductVariant $variant, RemoveFromCart $removeFromCart): RedirectResponse
    {
        $removeFromCart->handle($variant->id);

        return back();
    }
}
