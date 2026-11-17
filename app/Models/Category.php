<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = ['department_id', 'name', 'slug', 'active'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
