<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddToCart;
use App\Actions\Cart\GetCartItems;
use App\Actions\Cart\RemoveFromCart;
use App\Actions\Cart\UpdateCartItem;
use App\Actions\Products\FindActiveProductBySlug;
use App\Models\Product;
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

    public function store(Request $request, FindActiveProductBySlug $findProduct, AddToCart $addToCart): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = $findProduct->handle($validated['slug']);

        $addToCart->handle($product, $validated['quantity'] ?? 1);

        return back();
    }

    public function update(Request $request, Product $product, UpdateCartItem $updateCartItem): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $updateCartItem->handle($product, $validated['quantity']);

        return back();
    }

    public function destroy(Product $product, RemoveFromCart $removeFromCart): RedirectResponse
    {
        $removeFromCart->handle($product->id);

        return back();
    }
}
