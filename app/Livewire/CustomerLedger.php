<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class CustomerLedger extends Component
{
    public Customer $customer;
    
    // حقول نموذج الدفع
    public $amount = '';
    public $payment_method = 'cash';
    public $transaction_number = '';
    public $notes = '';

    // دالة تهيئة المكون (تستقبل العميل من الرابط)
    public function mount(Customer $customer)
    {
        $this->customer = $customer;
    }

    // دالة إضافة دفعة جديدة (سند قبض)
    public function addPayment()
    {
        // التحقق من صحة المدخلات
        $this->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,bankak',
            'transaction_number' => 'required_if:payment_method,bankak',
        ], [
            'amount.required' => 'الرجاء إدخال المبلغ',
            'transaction_number.required_if' => 'رقم الإشعار مطلوب عند الدفع ببنكك',
        ]);

        DB::transaction(function () {
            // 1. تسجيل الدفعة في جدول المدفوعات
            Payment::create([
                'customer_id' => $this->customer->id,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'transaction_number' => $this->transaction_number,
                'payment_date' => now(),
                'notes' => $this->notes,
            ]);

            // 2. إنقاص الدين من رصيد العميل
            $this->customer->decrement('balance', $this->amount);
        });

        // 3. تفريغ الحقول وإظهار رسالة نجاح
        $this->reset(['amount', 'payment_method', 'transaction_number', 'notes']);
        session()->flash('success', 'تم استلام الدفعة وخصمها من المديونية بنجاح!');
        
        // تحديث بيانات العميل في الشاشة
        $this->customer->refresh();
    }

    public function render()
    {
        // جلب آخر 10 دفعات للعميل لعرضها في الجدول
        $payments = Payment::where('customer_id', $this->customer->id)
                           ->orderBy('created_at', 'desc')
                           ->take(10)
                           ->get();

        // جلب الفواتير الآجلة (للاطلاع فقط)
        $unpaidSales = $this->customer->sales()
                                      ->where('remaining_amount', '>', 0)
                                      ->orderBy('created_at', 'desc')
                                      ->get();

        return view('components.customer-ledger', compact('payments', 'unpaidSales'));
    }
}