<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Products\ListActiveProducts;
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
}
