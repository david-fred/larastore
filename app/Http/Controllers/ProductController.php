<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Products\ListActiveProducts;
use App\Actions\Products\FindActiveProductBySlug;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(ListActiveProducts $listActiveProducts): Response
    {
        $products = $listActiveProducts->handle()->through(fn (Product $product) => [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'price' => $product->price,
            'category' => $product->category->name,
            'image' => $product->images->first()?->url(),
        ]);

        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }

    public function show(string $slug, FindActiveProductBySlug $findProduct): Response
    {
        $product = $findProduct->handle($slug);

        return Inertia::render('Products/Show', [
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'category' => $product->category->name,
                'images' => $product->images->map(fn ($image) => $image->url())->all(),
            ],
        ]);
    }
}
