<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier; // 🌟 أضفنا موديل المورد
use Illuminate\Support\Facades\DB; // 🌟 أضفنا DB للمعاملات الآمنة

class PurchaseHistory extends Component
{
    use WithPagination;

    public $search_supplier = '';
    public $date_from = '';
    public $date_to = '';

    public $isModalOpen = false;
    public $selectedPurchase = null;
    public $purchaseItems = [];

    public $isPaymentModalOpen = false;
    public $purchaseToPay = null;
    public $pay_amount = 0;
    public $pay_method = 'cash';

    public function updatingSearchSupplier()
    {
        $this->resetPage();
    }

    public function openPaymentModal($id)
    {
        $this->purchaseToPay = Purchase::find($id);
        $this->pay_amount = $this->purchaseToPay->remaining_amount;
        $this->pay_method = 'cash';
        $this->isPaymentModalOpen = true;
    }

    public function submitPayment()
    {
        $this->validate([
            'pay_amount' => 'required|numeric|min:1|max:' . $this->purchaseToPay->remaining_amount,
        ]);

        DB::transaction(function () {
            $purchase = $this->purchaseToPay;
            $newPaid = $purchase->paid_amount + $this->pay_amount;
            $newRemaining = $purchase->total_amount - $newPaid;
            
            $status = $newRemaining <= 0 ? 'paid' : 'partial';

            $purchase->update([
                'paid_amount' => $newPaid,
                'remaining_amount' => $newRemaining,
                'payment_status' => $status,
            ]);

            // 🌟 تحديث حساب المورد عند السداد
            if ($purchase->supplier_id) {
                $supplier = Supplier::find($purchase->supplier_id);
                if ($supplier) {
                    $supplier->decrement('balance', $this->pay_amount);
                }
            }
        });

        $this->isPaymentModalOpen = false;
        session()->flash('success', 'تم تسجيل سداد دفعة وتحديث حساب المورد بنجاح!');
    }

    public function viewDetails($purchaseId)
    {
        $this->selectedPurchase = Purchase::find($purchaseId);
        
        $this->purchaseItems = PurchaseItem::with(['product', 'unit'])
            ->where('purchase_id', $purchaseId)
            ->get();
            
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedPurchase = null;
        $this->purchaseItems = [];
    }

    // ==========================================
    // 🌟 دالة إلغاء الفاتورة بالكامل وعكس القيود 🌟
    // ==========================================
    public function deletePurchase($id)
    {
        DB::transaction(function () use ($id) {
            $purchase = Purchase::with('items')->find($id);
            if (!$purchase) return;

            // 1. عكس حساب المورد (إذا كانت الفاتورة آجلة وبها دين)
            if ($purchase->supplier_id && $purchase->remaining_amount > 0) {
                $supplier = Supplier::find($purchase->supplier_id);
                if ($supplier) {
                    $supplier->decrement('balance', $purchase->remaining_amount);
                }
            }

            // 2. حذف الأصناف فرادى (هذا السطر سيستدعي الـ Observer ليخصم المخزون أوتوماتيكياً!)
            foreach ($purchase->items as $item) {
                $item->delete(); 
            }

            // 3. حذف الفاتورة الأم
            $purchase->delete();
        });

        $this->closeModal();
        session()->flash('success', 'تم إلغاء الفاتورة بنجاح. تم خصم البضاعة من المخزن وعكس حساب المورد.');
    }

    public function render()
    {
        $query = Purchase::query();

        if (!empty($this->search_supplier)) {
            $query->where('supplier_name', 'like', '%' . $this->search_supplier . '%');
        }

        if (!empty($this->date_from)) {
            $query->whereDate('purchase_date', '>=', $this->date_from);
        }

        if (!empty($this->date_to)) {
            $query->whereDate('purchase_date', '<=', $this->date_to);
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('components.purchase-history', [
            'purchases' => $purchases,
        ])->layout('layouts.app');
    }
}