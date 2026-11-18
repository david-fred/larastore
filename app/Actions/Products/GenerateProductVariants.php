<?php


namespace App\Actions\Products;

use App\Models\Product;

class GenerateProductVariants
{
    public function handle(Product $product): void
    {
        $groups = $product->variationTypes()
            ->with('options')
            ->get()
            ->map(fn($type) => $type->options->pluck('id')->all())
            ->filter(fn(array $ids) => count($ids) > 0)
            ->values()
            ->all();

        // Every combination the product should have, keyed by its option IDs
        $wanted = [];

        foreach ($this->combinations($groups) as $optionIds) {
            $wanted[$this->key($optionIds)] = $optionIds;
        }

        $seen = [];

        foreach ($product->variants()->with('options')->get() as $variant) {
            $key = $this->key($variant->options->pluck('id')->all());

            // Delete variants whose options were removed, plus duplicates
            if (!array_key_exists($key, $wanted) || array_key_exists($key, $seen)) {
                $variant->delete();
                continue;
            }

            $seen[$key] = true;
        }

        foreach ($wanted as $key => $optionIds) {
            if (array_key_exists($key, $seen)) {
                continue;
            }

            $variant = $product->variants()->create([
                'price' => $product->price,
                // A product with no variation types gets one default variant that uses its stock
                'quantity' => $optionIds === [] ? $product->quantity : 0,
            ]);

            $variant->options()->attach($optionIds);
        }
    }

    private function combinations(array $groups): array
    {
        $combinations = [[]];

        foreach ($groups as $group) {
            $next = [];

            foreach ($combinations as $combination) {
                foreach ($group as $optionId) {
                    $next[] = [...$combination, $optionId];
                }
            }

            $combinations = $next;
        }

        return $combinations;
    }

    private function key(array $optionIds): string
    {
        sort($optionIds);

        return implode(',', $optionIds);
    }
}
