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

        // 1. حركة المبيعات والمصروفات
        $todaySales = Sale::whereDate('created_at', $today)->sum('total_amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        $todayPurchases = Purchase::whereDate('purchase_date', $today)->sum('total_amount');

        // 2. حساب تكلفة البضاعة المباعة اليوم
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
        // 4. السحر المحاسبي: فصل النقدية (الدرج) عن حساب (بنكك)
        // -------------------------------------------------------------
        
        // أ) المبيعات مفصلة حسب طريقة الدفع
        $cashSales = Sale::whereDate('created_at', $today)->where('payment_method', 'cash')->sum('paid_amount');
        $bankakSales = Sale::whereDate('created_at', $today)->where('payment_method', 'bankak')->sum('paid_amount');

        // ب) الدفعات المستلمة من الديون (نفترض حالياً أنها تدفع كاش، ويمكنك تطويرها لاحقاً لتقبل بنكك)
        $todayPayments = Payment::whereDate('created_at', $today)->sum('amount');

        // ج) الحسبة النهائية للمقبوضات
        $totalBankakReceived = $bankakSales; 
        
        // د) الكاش الفعلي في الخزنة = (مبيعات الكاش + سداد الديون) - (المصروفات اليومية المسحوبة من الدرج)
        $actualCashInDrawer = ($cashSales + $todayPayments) - $todayExpenses;
        
        // الإجمالي العام (اختياري للعرض)
        $totalReceivedToday = $actualCashInDrawer + $totalBankakReceived + $todayExpenses; 

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
            
            // المتغيرات الجديدة التي سنرسلها للواجهة
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