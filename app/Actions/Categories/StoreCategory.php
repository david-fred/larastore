<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Support\Str;

class StoreCategory
{
    public function handle(array $data): Category
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        return Category::create($data);
    }
}
