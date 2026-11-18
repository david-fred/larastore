<?php

namespace App\Actions\Products;

use App\Models\ProductVariationOption;
use Illuminate\Support\Facades\Storage;

class SyncOptionImages
{
    public function handle(ProductVariationOption $option, array $paths): void
    {
        $paths = collect($paths)->filter()->values();

        $option->loadMissing('type');

        // Remove photos that were deleted from this option
        $option->images()
            ->whereNotIn('path', $paths->all())
            ->get()
            ->each(function ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            });

        foreach ($paths as $order => $path) {
            $option->images()->updateOrCreate(
                ['path' => $path],
                [
                    'product_id' => $option->type->product_id,
                    'sort_order' => $order,
                ],
            );
        }
    }
}
