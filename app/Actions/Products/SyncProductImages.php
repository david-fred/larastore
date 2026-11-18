<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class SyncProductImages
{
    public function handle(Product $product, array $gallery): void
    {
        $items = collect($gallery)
            ->filter(fn ($item) => ! empty($item['path']))
            ->values();

        // Remove images that were deleted in the form
        $product->images()
            ->whereNotIn('path', $items->pluck('path')->all())
            ->get()
            ->each(function ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            });

        // Only accept option IDs that belong to this product
        $validOptionIds = $product->variationTypes()
            ->where('affects_images', true)
            ->with('options')
            ->get()
            ->flatMap(fn ($type) => $type->options->pluck('id'))
            ->all();

        foreach ($items as $order => $item) {
            $optionId = isset($item['option_id']) && in_array((int) $item['option_id'], $validOptionIds, true)
                ? (int) $item['option_id']
                : null;

            $product->images()->updateOrCreate(
                ['path' => $item['path']],
                [
                    'sort_order' => $order,
                    'product_variation_option_id' => $optionId,
                ],
            );
        }
    }
}
