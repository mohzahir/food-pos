<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    // السماح بإدخال البيانات لكل الحقول
    protected $guarded = [];

    // المنتجات التي تستخدم هذه الوحدة كوحدة أساسية
    public function baseProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'base_unit_id');
    }

    // تفاصيل ربط هذه الوحدة مع المنتجات (للوحدات الفرعية)
    public function productUnits(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }
}