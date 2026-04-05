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
                $this->return_quantities[$item->id] = '';
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

            // 1. إعادة الكمية إلى المخزون بأمان (الكمية × معامل التحويل)
            $conversionRate = $item->unit ? $item->unit->conversion_rate : 1;
            $stockToAdd = $qtyToReturn * $conversionRate;
            $item->product->increment('current_stock', $stockToAdd);

            // 2. تحديث بيانات الصنف داخل الفاتورة
            // 🌟 نستخدم withoutEvents لإيقاف الـ Observer مؤقتاً حتى لا يضيف البضاعة للمخزن مرتين عند حذف الصنف!
            SaleItem::withoutEvents(function () use ($item, $qtyToReturn, $refundAmount) {
                if ($qtyToReturn == $item->quantity) {
                    $item->delete(); // إرجاع كامل الصنف
                } else {
                    $item->quantity -= $qtyToReturn;
                    $item->subtotal -= $refundAmount;
                    $item->save(); // إرجاع جزئي
                }
            });

            // 3. المعالجة المالية (تطبيق خوارزمية الشلال المحاسبي)
            $sale->total_amount -= $refundAmount;
            $amountToRefund = $refundAmount;

            // أ) الأولوية الأولى: تسديد الديون (نسامح العميل في دينه أولاً)
            if ($amountToRefund > 0 && $sale->remaining_amount > 0) {
                $debtReduction = min($amountToRefund, $sale->remaining_amount);
                $sale->remaining_amount -= $debtReduction;
                $amountToRefund -= $debtReduction;

                if ($sale->customer_id) {
                    $customer = Customer::find($sale->customer_id);
                    if ($customer) {
                        $customer->decrement('balance', $debtReduction); // تنقيص دين العميل في دفتره
                    }
                }
            }

            // ب) الأولوية الثانية: إرجاع الأموال من الكاش (إذا كان قد دفع كاش)
            if ($amountToRefund > 0 && $sale->paid_cash > 0) {
                $cashRefund = min($amountToRefund, $sale->paid_cash);
                $sale->paid_cash -= $cashRefund;
                $amountToRefund -= $cashRefund;
            }

            // ج) الأولوية الثالثة: إرجاع الأموال من بنكك (إذا كان قد دفع عبر التطبيق)
            if ($amountToRefund > 0 && $sale->paid_bankak > 0) {
                $bankakRefund = min($amountToRefund, $sale->paid_bankak);
                $sale->paid_bankak -= $bankakRefund;
                $amountToRefund -= $bankakRefund;
            }

            // تحديث الإجمالي المدفوع الجديد (مجموع الكاش وبنكك المتبقي)
            $sale->paid_amount = $sale->paid_cash + $sale->paid_bankak;

            // تحديث حالة الفاتورة
            if ($sale->total_amount == 0) {
                $sale->payment_status = 'refunded'; // تم إرجاعها بالكامل
            } elseif ($sale->remaining_amount == 0) {
                $sale->payment_status = 'paid';
            } else {
                $sale->payment_status = 'partial';
            }

            $sale->save();
        });

        session()->flash('success', 'تم إرجاع الصنف بنجاح، وتحديث المخزون وتسوية حسابات (الكاش/بنكك)!');
        
        // تحديث عرض الفاتورة بعد الإرجاع ليرى الكاشير الأرقام الجديدة
        $this->sale = Sale::with('items.product', 'items.unit', 'customer')
                          ->where('receipt_number', $this->receipt_number)
                          ->first();
                          
        // تصفير المربعات استعداداً لأي إرجاع آخر
        if ($this->sale) {
            foreach ($this->sale->items as $i) {
                $this->return_quantities[$i->id] = '';
            }
        }
    }

    public function render()
    {
        return view('components.return-screen');
    }
}