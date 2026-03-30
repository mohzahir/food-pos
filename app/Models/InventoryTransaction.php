<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $guarded = [];

    // علاقة الحركة بالمنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // علاقة الحركة بالمستخدم (الكاشير أو المدير)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}