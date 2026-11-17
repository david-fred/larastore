<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ListActiveProducts
{
    public function handle(int $perPage = 12): LengthAwarePaginator
    {
        return Product::where('active', true)
            ->with(['images', 'category'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
