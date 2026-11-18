<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'price', 'quantity', 'sku'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariationOption::class,
            'product_variant_option',
            'product_variant_id',
            'product_variation_option_id',
        );
    }

    // "Silver / 128GB", ordered by the variation type order
    public function getLabelAttribute(): string
    {
        $this->loadMissing('options.type');

        $label = $this->options
            ->sortBy(fn ($option) => $option->type->sort_order)
            ->pluck('name')
            ->implode(' / ');

        return $label !== '' ? $label : 'Default';
    }

    public function getDisplayNameAttribute(): string
    {
        $this->loadMissing('product');

        return $this->label === 'Default'
            ? $this->product->title
            : "{$this->product->title} ({$this->label})";
    }
}
