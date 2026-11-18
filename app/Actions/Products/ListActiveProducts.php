<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ListActiveProducts
{
    public function handle(int $perPage = 12, ?int $categoryId = null): LengthAwarePaginator
    {
        return Product::where('active', true)
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->with(['images', 'category'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
