<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariationType extends Model
{
    protected $fillable = ['product_id', 'name', 'sort_order', 'affects_images'];

    protected function casts(): array
    {
        return [
            'affects_images' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductVariationOption::class, 'variation_type_id')->orderBy('sort_order');
    }
}
