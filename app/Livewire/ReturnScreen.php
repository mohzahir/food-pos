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
    
    // مصفوفة لحفظ الكمية التي يريد الكاشير إرجاعها من كل صنف
    public $return_quantities = []; 

    // البحث عن الفاتورة
    public function searchInvoice()
    {
        $this->validate([
            'receipt_number' => 'required'
        ], [
            'receipt_number.required' => 'الرجاء إدخال رقم الفاتورة'
        ]);

        $this->sale = Sale::with('items.product', 'items.unit', 'customer')
                          ->where('receipt_number', $this->receipt_number)
                          ->first();

        if (!$this->sale) {
            session()->flash('error', 'لم يتم العثور على الفاتورة، تأكد من الرقم!');
            $this->return_quantities = [];
        } else {
            // تصفير كميات الإرجاع عند فتح الفاتورة
            foreach ($this->sale->items as $item) {
                $this->return_quantities[$item->id] = 0;
            }
        }
    }

    // تنفيذ عملية الإرجاع لصنف محدد
    public function processReturn($itemId)
    {
        $qtyToReturn = (float) ($this->return_quantities[$itemId] ?? 0);
        
        if ($qtyToReturn <= 0) {
            session()->flash('error', 'يجب إدخال كمية صحيحة للإرجاع!');
            return;
        }

        $item = SaleItem::with('product', 'unit', 'sale')->find($itemId);

        if (!$item || $qtyToReturn > $item->quantity) {
            session()->flash('error', 'الكمية المرتجعة أكبر من الكمية المشتراة في الفاتورة!');
            return;
        }

        DB::transaction(function () use ($item, $qtyToReturn) {
            $refundAmount = $qtyToReturn * $item->unit_price;
            $sale = $item->sale;

            // 1. إعادة الكمية إلى المخزون (الكمية × معامل تحويل الوحدة)
            $stockToAdd = $qtyToReturn * $item->unit->conversion_rate;
            $item->product->increment('current_stock', $stockToAdd);

            // 2. تحديث بيانات الصنف داخل الفاتورة
            $item->quantity -= $qtyToReturn;
            $item->subtotal -= $refundAmount;
            $item->save();

            // 3. المعالجة المالية (تحديث الفاتورة والديون)
            $sale->total_amount -= $refundAmount;

            if ($sale->customer_id && $sale->remaining_amount > 0) {
                // إذا كان للعميل دين في هذه الفاتورة، نخصم المرتجع من الدين أولاً
                $customer = Customer::find($sale->customer_id);
                
                if ($refundAmount <= $sale->remaining_amount) {
                    $sale->remaining_amount -= $refundAmount;
                    $customer->decrement('balance', $refundAmount);
                } else {
                    // إذا كان المرتجع أكبر من الدين المتبقي (أرجع بضاعة أكثر مما عليه)
                    $cashToRefund = $refundAmount - $sale->remaining_amount;
                    $customer->decrement('balance', $sale->remaining_amount); // تصفير الدين
                    $sale->remaining_amount = 0;
                    $sale->paid_amount -= $cashToRefund; // إرجاع الباقي كاش
                }
            } else {
                // فاتورة نقدية أو مسددة بالكامل: يتم إرجاع المبلغ كاش
                $sale->paid_amount -= $refundAmount;
            }

            // تحديث حالة الفاتورة
            if ($sale->total_amount == 0) {
                $sale->payment_status = 'paid'; // فاتورة ملغية فعلياً
            } elseif ($sale->remaining_amount <= 0) {
                $sale->payment_status = 'paid';
            } elseif ($sale->remaining_amount == $sale->total_amount) {
                $sale->payment_status = 'unpaid';
            } else {
                $sale->payment_status = 'partial';
            }

            $sale->save();

            // إذا أرجع الكاشير كل الكمية، نحذف الصنف من الفاتورة نهائياً
            if ($item->quantity == 0) {
                $item->delete();
            }
        });

        session()->flash('success', 'تم إرجاع الصنف، وإعادته للمخزن، وتحديث الحسابات بنجاح!');
        $this->searchInvoice(); // تحديث عرض الفاتورة بعد الإرجاع
    }

    public function render()
    {
        return view('components.return-screen');
    }
}