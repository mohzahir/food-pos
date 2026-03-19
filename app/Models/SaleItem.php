<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    // السماح بإدخال البيانات
    protected $guarded = [];

    // العلاقة مع الفاتورة الأساسية
    public function sale()
    {
        return $this->belongsTo(Sale::class);
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