<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // السماح بحفظ البيانات في جميع الحقول
    protected $guarded = [];

    // العلاقة مع العميل (صاحب الدفعة)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}