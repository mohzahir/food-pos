<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Purchase;
use App\Models\PurchaseItem;

class PurchaseHistory extends Component
{
    use WithPagination;

    // متغيرات للبحث والفلترة
    public $search_supplier = '';
    public $date_from = '';
    public $date_to = '';

    // متغيرات النافذة المنبثقة (Modal) لعرض التفاصيل
    public $isModalOpen = false;
    public $selectedPurchase = null;
    public $purchaseItems = [];

    // متغيرات سداد الدين
    public $isPaymentModalOpen = false;
    public $purchaseToPay = null;
    public $pay_amount = 0;
    public $pay_method = 'cash';

    // تصفير الصفحة عند البحث
    public function updatingSearchSupplier()
    {
        $this->resetPage();
    }


    // فتح نافذة السداد
    public function openPaymentModal($id)
    {
        $this->purchaseToPay = Purchase::find($id);
        $this->pay_amount = $this->purchaseToPay->remaining_amount; // افتراضياً يسدد كل المتبقي
        $this->pay_method = 'cash';
        $this->isPaymentModalOpen = true;
    }

    // حفظ عملية السداد
    public function submitPayment()
    {
        $this->validate([
            'pay_amount' => 'required|numeric|min:1|max:' . $this->purchaseToPay->remaining_amount,
        ]);

        $purchase = $this->purchaseToPay;
        $newPaid = $purchase->paid_amount + $this->pay_amount;
        $newRemaining = $purchase->total_amount - $newPaid;
        
        $status = $newRemaining <= 0 ? 'paid' : 'partial';

        $purchase->update([
            'paid_amount' => $newPaid,
            'remaining_amount' => $newRemaining,
            'payment_status' => $status,
        ]);

        // (ملاحظة: هنا سيتم إضافة كود خصم المبلغ من الخزينة المركزية أو بنكك لاحقاً)

        $this->isPaymentModalOpen = false;
        session()->flash('success', 'تم تسجيل سداد دفعة للمورد بنجاح!');
    }

    // فتح الفاتورة لعرض أصنافها
    public function viewDetails($purchaseId)
    {
        $this->selectedPurchase = Purchase::find($purchaseId);
        
        // جلب الأصناف مع علاقات المنتج والوحدة لتجنب تكرار الاستعلامات
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

    public function render()
    {
        // بناء استعلام الفواتير مع الفلترة
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

        // جلب الفواتير مرتبة من الأحدث للأقدم
        $purchases = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('components.purchase-history', [
            'purchases' => $purchases,
        ])->layout('layouts.app'); // تأكد من أن هذا يتوافق مع تخطيط موقعك
    }
}