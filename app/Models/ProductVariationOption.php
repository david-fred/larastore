<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariationOption extends Model
{
    protected $fillable = ['variation_type_id', 'name', 'sort_order'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductVariationType::class, 'variation_type_id');
    }
}
