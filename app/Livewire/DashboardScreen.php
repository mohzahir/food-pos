<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\PurchaseItem;
use Carbon\Carbon;

class DashboardScreen extends Component
{
    public function render()
    {
        $today = Carbon::today();

        // 1. حركة المبيعات والمصروفات والمشتريات (الإجماليات العامة)
        $todaySales = Sale::whereDate('created_at', $today)->sum('total_amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        $todayPurchases = Purchase::whereDate('purchase_date', $today)->sum('total_amount');

        // 2. حساب تكلفة البضاعة المباعة اليوم (لحساب الربح)
        $todaySaleItems = SaleItem::with(['product', 'unit'])->whereHas('sale', function($q) use ($today) {
            $q->whereDate('created_at', $today);
        })->get();

        $costOfGoodsSold = 0;
        foreach($todaySaleItems as $item) {
            if($item->product && $item->unit) {
                $itemCost = $item->product->current_cost_price * $item->unit->conversion_rate * $item->quantity;
                $costOfGoodsSold += $itemCost;
            }
        }

        // 3. الربح الصافي
        $netProfit = $todaySales - $costOfGoodsSold - $todayExpenses;

        // -------------------------------------------------------------
        // 4. السحر المحاسبي (المحدث): فصل النقدية عن بنكك بدقة متناهية
        // -------------------------------------------------------------
        
        // أ) مقبوضات المبيعات: نجمع حقول الدفع المباشرة (هذا يحل مشكلة الدفع المختلط Split فوراً)
        $todayCashSales = Sale::whereDate('created_at', $today)->sum('paid_cash');
        $todayBankakSales = Sale::whereDate('created_at', $today)->sum('paid_bankak');

        // ب) مدفوعات المصروفات: فصل منصرفات الكاش عن بنكك
        $todayCashExpenses = Expense::whereDate('expense_date', $today)->sum('paid_cash');
        $todayBankakExpenses = Expense::whereDate('expense_date', $today)->sum('paid_bankak');

        // ج) مدفوعات المشتريات (للموردين): فصل ما دفعناه للموردين اليوم
        $todayCashPurchases = Purchase::whereDate('purchase_date', $today)->sum('paid_cash');
        $todayBankakPurchases = Purchase::whereDate('purchase_date', $today)->sum('paid_bankak');

        // د) الدفعات المستلمة من سداد الديون (نفترض حالياً أنها كاش)
        $todayPayments = Payment::whereDate('created_at', $today)->sum('amount');

        // هـ) الحسبة النهائية الدقيقة:
        
        // الكاش الفعلي في الدرج = (مبيعات كاش + سداد ديون) - (مصروفات كاش + سداد موردين كاش)
        $actualCashInDrawer = ($todayCashSales + $todayPayments) - ($todayCashExpenses + $todayCashPurchases);
        
        // صافي رصيد بنكك اليوم = مبيعات بنكك - (مصروفات بنكك + سداد موردين بنكك)
        $totalBankakReceived = $todayBankakSales - ($todayBankakExpenses + $todayBankakPurchases); 
        
        // الإجمالي العام المتوفر (كاش + بنكك)
        $totalReceivedToday = $actualCashInDrawer + $totalBankakReceived; 

        // -------------------------------------------------------------

        // 5. إجمالي الديون بالسوق
        $totalDebts = Customer::sum('balance');

        // 6. التنبيهات الذكية (النواقص والصلاحية)
        $lowStockProducts = Product::where('current_stock', '<=', 10)->where('is_active', true)->take(5)->get();
        $expiringCount = PurchaseItem::whereNotNull('expiry_date')
                                     ->whereDate('expiry_date', '<=', Carbon::today()->addDays(30))
                                     ->count();

        // 7. آخر المبيعات
        $recentSales = Sale::with('customer')->orderBy('created_at', 'desc')->take(5)->get();

        return view('components.dashboard-screen', [
            'todaySales' => $todaySales,
            'todayExpenses' => $todayExpenses,
            'todayPurchases' => $todayPurchases,
            'netProfit' => $netProfit,
            
            // المتغيرات المحدثة للواجهة
            'actualCashInDrawer' => $actualCashInDrawer,
            'totalBankakReceived' => $totalBankakReceived,
            'totalReceivedToday' => $totalReceivedToday,
            
            'totalDebts' => $totalDebts,
            'lowStockProducts' => $lowStockProducts,
            'expiringCount' => $expiringCount,
            'recentSales' => $recentSales,
        ]);
    }
}