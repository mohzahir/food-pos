<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\InventoryTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // أبقينا هذه في حال احتجتها لاحقاً

class DailyProfitReport extends Component
{
    public $selectedDate;

    public function mount()
    {
        // افتراضياً نعرض أرباح اليوم الحالي
        $this->selectedDate = Carbon::today()->format('Y-m-d');
    }

    public function render()
    {
        // 1. إجمالي المبيعات لليوم المحدد
        $totalSales = Sale::whereDate('created_at', $this->selectedDate)->sum('total_amount');

        // ========================================================
        // 🌟 2. تكلفة البضاعة المباعة (الطريقة الآمنة والمحدثة) 🌟
        // ========================================================
        $saleItems = SaleItem::with('product')->whereHas('sale', function($query) {
            $query->whereDate('created_at', $this->selectedDate);
        })->get();

        $costOfGoodsSold = $saleItems->sum(function ($item) {
            // يبحث عن سعر التكلفة المسجل في الفاتورة (cost_price)، وإذا لم يجده يأخذه من كارت المنتج الأصلي
            // (الافتراضي 0 لتجنب أي أخطاء إذا كان المنتج محذوفاً)
            $cost = $item->cost_price ?? ($item->product ? $item->product->current_cost_price : 0);
            
            // حساب التكلفة: (الكمية المباعة) × التكلفة المحفوظة
            // ملاحظة: لقد حفظنا التكلفة كـ (التكلفة الأساسية × معامل التحويل) في خطوة الكاشير، 
            // لذا هنا نضرب في الكمية فقط.
            return $item->quantity * $cost;
        });
        // ========================================================

        // 3. مجمل الربح (المبيعات - التكلفة)
        $grossProfit = $totalSales - $costOfGoodsSold;

        // 4. المصروفات اليومية
        $totalExpenses = Expense::whereDate('created_at', $this->selectedDate)->sum('amount');

        // 5. تسويات المخزون (توالف وزيادات)
        $inventoryAdjustments = InventoryTransaction::where('type', 'reconciliation')
            ->whereDate('created_at', $this->selectedDate)
            ->sum('total_value');

        // 6. صافي الربح النهائي
        $netProfit = $grossProfit - $totalExpenses + $inventoryAdjustments;

        // جلب تفاصيل المصروفات لعرضها في الجدول الجانبي
        $expensesList = Expense::whereDate('created_at', $this->selectedDate)->latest()->get();

        return view('components.daily-profit-report', [
            'totalSales' => $totalSales,
            'costOfGoodsSold' => $costOfGoodsSold,
            'grossProfit' => $grossProfit,
            'totalExpenses' => $totalExpenses,
            'inventoryAdjustments' => $inventoryAdjustments,
            'netProfit' => $netProfit,
            'expensesList' => $expensesList,
        ])->layout('layouts.app');
    }
}