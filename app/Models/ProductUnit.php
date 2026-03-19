<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnit extends Model
{
    protected $guarded = [];

    // العلاقة مع المنتج
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // العلاقة مع الوحدة
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}