<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Support\Str;

class UpdateCategory
{
    public function handle(Category $category, array $data): Category
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category->update($data);

        return $category;
    }
}
