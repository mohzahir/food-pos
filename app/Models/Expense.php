<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'description', 
        'category', 
        'amount', 
        'expense_date', 
        'paid_cash',          // جديد
        'paid_bankak',        // جديد
        'payment_method',     // جديد
        'transaction_number'  // جديد
    ];
}