<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // السماح بحفظ البيانات في كل الحقول
    protected $guarded = [];

    // العلاقة لجلب جميع الفواتير (المبيعات) الخاصة بهذا العميل
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // العلاقة لجلب جميع المدفوعات (سندات القبض) التي دفعها العميل
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}