<?php

namespace App\Http\Controllers;

use App\Actions\Products\FindActiveProductBySlug;
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
                'category' => $product->category->name,
                'images' => $product->images->map(fn ($image) => $image->url())->all(),
                'variationTypes' => $product->variationTypes->map(fn ($type) => [
                    'id' => $type->id,
                    'name' => $type->name,
                    'options' => $type->options->map(fn ($option) => [
                        'id' => $option->id,
                        'name' => $option->name,
                    ])->values(),
                ])->values(),
                'variants' => $product->variants->map(fn ($variant) => [
                    'id' => $variant->id,
                    'label' => $variant->label,
                    'price' => $variant->price,
                    'quantity' => $variant->quantity,
                    'option_ids' => $variant->options->pluck('id')->all(),
                ])->values(),
            ],
        ]);
    }
}
