<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariationOption extends Model
{
    protected $fillable = ['variation_type_id', 'name', 'sort_order'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductVariationType::class, 'variation_type_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_variation_option_id')->orderBy('sort_order');
    }
}
