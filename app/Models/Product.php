<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = [];

    // 1. الوحدة الأساسية للمنتج (مثلاً: جرام أو حبة)
    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    // 2. الوحدات الفرعية للمنتج (مثلاً: كرتونة، كيلو، رطل)
    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    // 3. دالة ذكية لحساب السعر الفعلي لأي وحدة أثناء البيع
    public function getPriceForUnit($unitId)
    {
        // إذا كانت الوحدة المطلوبة هي الوحدة الأساسية، نرجع السعر الأساسي مباشرة
        if ($this->base_unit_id == $unitId) {
            return $this->current_selling_price;
        }

        // نبحث هل هناك تسعيرة خاصة (سعر جملة) لهذه الوحدة في جدول product_units؟
        $productUnit = $this->units()->where('unit_id', $unitId)->first();
        
        if ($productUnit && $productUnit->specific_selling_price > 0) {
            // إذا وجدنا سعر مخصص (مثلاً الكرتونة بـ 5000 بغض النظر عن سعر الحبة)، نرجعه
            return $productUnit->specific_selling_price;
        }

        // إذا لم يكن هناك سعر مخصص، نحسب السعر بناءً على معامل التحويل (conversion_rate)
        // مثلاً: سعر الجرام 5 جنيه * معامل تحويل الكيلو (1000) = 5000 جنيه
        $unit = Unit::find($unitId);
        if ($unit) {
            return $this->current_selling_price * $unit->conversion_rate;
        }

        return 0; // في حال حدوث خطأ
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    // 👇 الكود الجديد الذي يحل المشكلة 👇
    // علاقة المنتج بتسعيرات ووحدات الجملة (الباركودات الفرعية)
    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }
    // 👆 نهاية الكود الجديد 👆
}