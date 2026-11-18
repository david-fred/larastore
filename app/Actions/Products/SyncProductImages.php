<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Collection;
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

        // Types are synced before this runs, so new options already have IDs
        $imageOptions = $product->variationTypes()
            ->where('affects_images', true)
            ->with('options')
            ->get()
            ->flatMap(fn ($type) => $type->options);

        foreach ($items as $order => $item) {
            $product->images()->updateOrCreate(
                ['path' => $item['path']],
                [
                    'sort_order' => $order,
                    'product_variation_option_id' => $this->resolveOptionId($item['option_key'] ?? null, $imageOptions),
                ],
            );
        }
    }

    private function resolveOptionId(?string $key, Collection $options): ?int
    {
        if (! $key) {
            return null;
        }

        [$kind, $value] = array_pad(explode(':', $key, 2), 2, null);

        if ($kind === 'id') {
            return $options->firstWhere('id', (int) $value)?->id;
        }

        if ($kind === 'new') {
            return $options->firstWhere('name', trim((string) $value))?->id;
        }

        return null;
    }
}
