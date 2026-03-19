<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseManager extends Component
{
    // متغيرات إضافة منصرف جديد
    public $description = '';
    public $category = 'نثريات';
    
    // متغيرات الدفع الجديدة
    public $paid_cash = '';
    public $paid_bankak = '';
    public $transaction_number = '';
    
    public $filter_date;

    public function mount()
    {
        $this->filter_date = Carbon::today()->toDateString();
    }

    public function addExpense()
    {
        $this->validate([
            'description' => 'required|string|max:255',
            'category' => 'required|string',
            'paid_cash' => 'nullable|numeric|min:0',
            'paid_bankak' => 'nullable|numeric|min:0',
        ], [
            'description.required' => 'الرجاء كتابة تفاصيل المنصرف',
        ]);

        $cash = (float) $this->paid_cash;
        $bankak = (float) $this->paid_bankak;
        $total_amount = $cash + $bankak;

        // التحقق من إدخال مبلغ
        if ($total_amount <= 0) {
            $this->addError('payment', 'يجب إدخال مبلغ المنصرف في الكاش أو بنكك!');
            return;
        }

        // تحديد طريقة الدفع
        $method = 'cash';
        if ($cash > 0 && $bankak > 0) $method = 'split';
        elseif ($bankak > 0) $method = 'bankak';

        Expense::create([
            'description' => $this->description,
            'amount' => $total_amount,
            'paid_cash' => $cash,
            'paid_bankak' => $bankak,
            'payment_method' => $method,
            'transaction_number' => $this->transaction_number,
            'category' => $this->category,
            'expense_date' => $this->filter_date, 
        ]);

        session()->flash('success', 'تم تسجيل المنصرف وخصمه من الخزينة بنجاح!');
        
        $this->reset(['description', 'paid_cash', 'paid_bankak', 'transaction_number']);
        $this->category = 'نثريات';
    }

    public function deleteExpense($id)
    {
        Expense::findOrFail($id)->delete();
        session()->flash('success', 'تم حذف المنصرف وإرجاع المبلغ للخزينة بنجاح!');
    }

    public function render()
    {
        $expensesList = Expense::whereDate('expense_date', $this->filter_date)->latest()->get();
        $totalExpenses = $expensesList->sum('amount');

        return view('components.expense-manager', [
            'expensesList' => $expensesList,
            'totalExpenses' => $totalExpenses,
        ])->layout('layouts.app');
    }
}