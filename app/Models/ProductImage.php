<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'path', 'sort_order', 'product_variation_option_id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variationOption(): BelongsTo
    {
        return $this->belongsTo(ProductVariationOption::class, 'product_variation_option_id');
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
