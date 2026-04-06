<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class ReturnScreen extends Component
{
    public $receipt_number = '';
    public $sale = null;
    public $return_quantities = []; 

    // 🌟 متغيرات نافذة الإرجاع المرنة
    public $isRefundModalOpen = false;
    public $item_to_return_id = null;
    public $qty_to_return = 0;
    
    public $total_refund_amount = 0;
    public $debt_to_deduct = 0;
    public $amount_to_pay_customer = 0;
    
    public $refund_cash = 0;
    public $refund_bankak = 0;

    public function searchInvoice()
    {
        $this->validate(['receipt_number' => 'required'], ['receipt_number.required' => 'الرجاء إدخال رقم الفاتورة']);

        $this->sale = Sale::with('items.product', 'items.unit', 'customer')
                          ->where('receipt_number', $this->receipt_number)
                          ->first();

        if (!$this->sale) {
            session()->flash('error', 'لم يتم العثور على الفاتورة، تأكد من الرقم!');
            $this->return_quantities = [];
        } else {
            foreach ($this->sale->items as $item) {
                $this->return_quantities[$item->id] = '';
            }
        }
    }

    // 🌟 دالة فتح نافذة التأكيد (بدلاً من الإرجاع المباشر)
    public function processReturn($itemId)
    {
        $qtyToReturn = (float) ($this->return_quantities[$itemId] ?? 0);
        
        if ($qtyToReturn <= 0) {
            session()->flash('error', 'يجب إدخال كمية صحيحة للإرجاع!');
            return;
        }

        $item = SaleItem::with('product', 'unit', 'sale')->find($itemId);

        if (!$item || $qtyToReturn > $item->quantity) {
            session()->flash('error', 'الكمية المرتجعة أكبر من الكمية المشتراة!');
            return;
        }

        // تجهيز الحسابات لعرضها للكاشير
        $this->item_to_return_id = $item->id;
        $this->qty_to_return = $qtyToReturn;
        $this->total_refund_amount = $qtyToReturn * $item->unit_price;

        $sale = $item->sale;

        // 1. حساب كم سنخصم من دينه (إجباري: الديون تُسدد أولاً)
        $this->debt_to_deduct = min($this->total_refund_amount, $sale->remaining_amount);
        
        // 2. حساب المبلغ المتبقي الذي يجب أن يدفعه الكاشير للعميل في يده
        $this->amount_to_pay_customer = $this->total_refund_amount - $this->debt_to_deduct;

        // افتراضياً، نجعل المبلغ المرتجع كاش (ويمكن للكاشير تغييره للبنك)
        $this->refund_cash = $this->amount_to_pay_customer;
        $this->refund_bankak = 0;

        // فتح النافذة
        $this->isRefundModalOpen = true;
    }

    // 🌟 دالة تنفيذ الإرجاع النهائي بعد اختيار الكاشير لطريقة الدفع
    public function confirmReturn()
    {
        // التحقق من أن الكاشير أدخل المبالغ بشكل صحيح بحيث تساوي المستحق للعميل
        if (round((float)$this->refund_cash + (float)$this->refund_bankak, 2) !== round((float)$this->amount_to_pay_customer, 2)) {
            session()->flash('modal_error', 'مجموع الإرجاع (كاش + بنكك) يجب أن يساوي المبلغ المستحق للعميل!');
            return;
        }

        DB::transaction(function () {
            $item = SaleItem::with('product', 'unit', 'sale')->find($this->item_to_return_id);
            $sale = $item->sale;

            // 1. إعادة الكمية إلى المخزون بأمان
            $conversionRate = $item->unit ? $item->unit->conversion_rate : 1;
            $item->product->increment('current_stock', $this->qty_to_return * $conversionRate);

            // 2. تحديث بيانات الصنف
            SaleItem::withoutEvents(function () use ($item) {
                if ($this->qty_to_return == $item->quantity) {
                    $item->delete(); 
                } else {
                    $item->quantity -= $this->qty_to_return;
                    $item->subtotal -= $this->total_refund_amount;
                    $item->save(); 
                }
            });

            // 3. المعالجة المالية المخصصة (حسب اختيار الكاشير)
            $sale->total_amount -= $this->total_refund_amount;

            // أ) تنقيص ديون العميل (إن وجدت)
            if ($this->debt_to_deduct > 0) {
                $sale->remaining_amount -= $this->debt_to_deduct;
                if ($sale->customer_id) {
                    Customer::find($sale->customer_id)->decrement('balance', $this->debt_to_deduct);
                }
            }

            // ب) خصم المبالغ من الكاش وبنكك بناءً على ما أدخله الكاشير!
            $sale->paid_cash -= (float) $this->refund_cash;
            $sale->paid_bankak -= (float) $this->refund_bankak;
            $sale->paid_amount = $sale->paid_cash + $sale->paid_bankak;

            // تحديث حالة الفاتورة
            if ($sale->total_amount == 0) {
                $sale->payment_status = 'refunded';
            } elseif ($sale->remaining_amount == 0) {
                $sale->payment_status = 'paid';
            } else {
                $sale->payment_status = 'partial';
            }

            $sale->save();
        });

        session()->flash('success', 'تم إرجاع الصنف بنجاح، وخصم المبلغ من الخزينة/البنك بدقة!');
        
        $this->isRefundModalOpen = false;
        $this->searchInvoice(); 
    }

    public function render()
    {
        return view('components.returns-screen');
    }
}