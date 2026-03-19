<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\TreasuryAdjustment;

class TreasuryDashboard extends Component
{
    public $adj_type = 'transfer_to_bank';
    public $adj_amount;
    public $adj_notes;

    public function saveAdjustment()
    {
        $this->validate([
            'adj_type' => 'required',
            'adj_amount' => 'required|numeric|min:1',
            'adj_notes' => 'nullable|string|max:255',
        ]);

        TreasuryAdjustment::create([
            'type' => $this->adj_type,
            'amount' => $this->adj_amount,
            'notes' => $this->adj_notes,
        ]);

        session()->flash('success', 'تم تسجيل الحركة المالية بنجاح!');
        $this->reset(['adj_amount', 'adj_notes']);
    }

    public function render()
    {
        // 1. حسابات المبيعات (الداخل)
        $totalSalesCash = Sale::sum('paid_cash');
        $totalSalesBankak = Sale::sum('paid_bankak');

        // 2. حسابات المشتريات والمصروفات (الخارج)
        $totalPurchasesCash = Purchase::sum('paid_cash');
        $totalPurchasesBankak = Purchase::sum('paid_bankak');
        $totalExpenses = Expense::sum('amount'); // نفترض أن كل المصروفات تُدفع كاش من الدرج حالياً

        // 3. التسويات اليدوية والتحويلات
        $depositsCash = TreasuryAdjustment::where('type', 'deposit_cash')->sum('amount');
        $withdrawalsCash = TreasuryAdjustment::where('type', 'withdrawal_cash')->sum('amount');
        
        $depositsBankak = TreasuryAdjustment::where('type', 'deposit_bankak')->sum('amount');
        $withdrawalsBankak = TreasuryAdjustment::where('type', 'withdrawal_bankak')->sum('amount');
        
        $transfersToBank = TreasuryAdjustment::where('type', 'transfer_to_bank')->sum('amount');
        $transfersToCash = TreasuryAdjustment::where('type', 'transfer_to_cash')->sum('amount');

        // 🧮 المعادلة النهائية لرصيد الكاش الفعلي في الدرج:
        $actualCashBalance = $totalSalesCash 
                           - $totalPurchasesCash 
                           - $totalExpenses 
                           + $depositsCash 
                           - $withdrawalsCash 
                           - $transfersToBank 
                           + $transfersToCash;

        // 🧮 المعادلة النهائية لرصيد حساب بنكك:
        $actualBankakBalance = $totalSalesBankak 
                             - $totalPurchasesBankak 
                             + $depositsBankak 
                             - $withdrawalsBankak 
                             + $transfersToBank 
                             - $transfersToCash;

        // جلب آخر الحركات لعرضها
        $recentAdjustments = TreasuryAdjustment::latest()->take(10)->get();

        return view('components.treasury-dashboard', [
            'actualCashBalance' => $actualCashBalance,
            'actualBankakBalance' => $actualBankakBalance,
            'recentAdjustments' => $recentAdjustments,
        ])->layout('layouts.app');
    }
}