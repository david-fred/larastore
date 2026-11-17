<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class SyncProductImages
{
    public function handle(Product $product, array $paths): void
    {
        $paths = array_values($paths);

        // Remove images that were deleted in the form
        $product->images()
            ->whereNotIn('path', $paths)
            ->get()
            ->each(function ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            });

        // Add new uploads and save the order from the form
        foreach ($paths as $order => $path) {
            $product->images()->updateOrCreate(
                ['path' => $path],
                ['sort_order' => $order],
            );
        }
    }
}
