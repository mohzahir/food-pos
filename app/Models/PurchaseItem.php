<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    // السماح بحفظ البيانات
    protected $guarded = [];

    // العلاقة مع الفاتورة الأساسية
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // العلاقة مع المنتج (هذه التي سببت الخطأ)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // العلاقة مع الوحدة (هذه التي سببت الخطأ)
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}