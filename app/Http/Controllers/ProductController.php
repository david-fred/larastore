<?php

namespace App\Http\Controllers;

use App\Actions\Products\FindActiveProductBySlug;
use App\Actions\Products\ListActiveProducts;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(ListActiveProducts $listActiveProducts): Response
    {
        return Inertia::render('Products/Index', [
            'products' => $this->cards($listActiveProducts->handle()),
        ]);
    }

    public function category(Category $category, ListActiveProducts $listActiveProducts): Response
    {
        return Inertia::render('Products/Index', [
            'category' => $category->name,
            'products' => $this->cards($listActiveProducts->handle(12, $category->id)),
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
                'categorySlug' => $product->category->slug,
                'images' => $product->images->map(fn ($image) => [
                    'url' => $image->url(),
                    'option_id' => $image->product_variation_option_id,
                ])->values(),
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

    private function cards(LengthAwarePaginator $products): LengthAwarePaginator
    {
        return $products->through(fn (Product $product) => [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'price' => $product->price,
            'category' => $product->category->name,
            'image' => $product->images->first(fn ($image) => $image->product_variation_option_id === null)?->url(),
        ]);
    }
}
