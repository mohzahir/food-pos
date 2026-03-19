<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    // السماح بحفظ البيانات
    protected $guarded = [];

    // العلاقة لجلب جميع الأصناف داخل هذه الفاتورة
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}