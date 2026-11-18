<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class SyncProductImages
{
    public function handle(Product $product, array $gallery): void
    {
        $paths = collect($gallery)->pluck('path')->filter()->values();

        // Remove general photos that were deleted from the list
        $product->images()
            ->whereNull('product_variation_option_id')
            ->whereNotIn('path', $paths->all())
            ->get()
            ->each(function ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            });

        foreach ($paths as $order => $path) {
            $product->images()->updateOrCreate(
                ['path' => $path],
                [
                    'sort_order' => $order,
                    'product_variation_option_id' => null,
                ],
            );
        }
    }
}
