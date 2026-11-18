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

        // Match "Show for" names to options across all variation types
        $options = $product->variationTypes()
            ->with('options')
            ->get()
            ->flatMap(fn ($type) => $type->options)
            ->keyBy('name');

        foreach ($items as $order => $item) {
            $optionName = trim((string) ($item['option_name'] ?? ''));

            $product->images()->updateOrCreate(
                ['path' => $item['path']],
                [
                    'sort_order' => $order,
                    'product_variation_option_id' => $optionName !== '' ? $options->get($optionName)?->id : null,
                ],
            );
        }
    }
}
