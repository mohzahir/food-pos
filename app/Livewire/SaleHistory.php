<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class SaleHistory extends Component
{
    use WithPagination;

    public $search_receipt = '';
    public $date_from = '';
    public $date_to = '';

    public $isModalOpen = false;
    public $selectedSale = null;
    public $saleItems = [];

    public function updatingSearchReceipt()
    {
        $this->resetPage();
    }

    public function viewDetails($saleId)
    {
        $this->selectedSale = Sale::find($saleId);
       
        $this->saleItems = SaleItem::with(['product', 'unit'])
            ->where('sale_id', $saleId)
            ->get();
           
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedSale = null;
        $this->saleItems = [];
    }

    // ==========================================
    // 🌟 دالة الإلغاء النهائي للفاتورة 🌟
    // ==========================================
    public function deleteSale($id)
    {
        DB::transaction(function () use ($id) {
            $sale = Sale::with('items')->find($id);
            if (!$sale) return;

            // 1. عكس ديون العميل (إذا كان عليه دين في هذه الفاتورة)
            if ($sale->customer_id && $sale->remaining_amount > 0) {
                $customer = Customer::find($sale->customer_id);
                if ($customer) {
                    $customer->decrement('balance', $sale->remaining_amount);
                }
            }

            // 2. حذف الأصناف فرادى (هذا سيشغل الـ Observer ليعيد البضاعة للمخزن!)
            foreach ($sale->items as $item) {
                $item->delete();
            }

            // 3. حذف الفاتورة
            $sale->delete();
        });

        $this->closeModal();
        session()->flash('success', 'تم إلغاء الفاتورة بنجاح وإعادة البضاعة للمخزن.');
    }

    // ==========================================
    // 🌟 دالة التعديل السحرية (إعادة الفاتورة للكاشير) 🌟
    // ==========================================
    public function editSale($id)
    {
        $sale = Sale::with(['items.product', 'items.unit'])->find($id);
        if (!$sale) return;

        $cartToRestore = [];
        foreach ($sale->items as $item) {
            $cartKey = $item->product_id . '_' . $item->unit_id;
            $originalPrice = $item->product ? ($item->product->current_selling_price * ($item->unit->conversion_rate ?? 1)) : $item->unit_price;

            $cartToRestore[$cartKey] = [
                'product_id' => $item->product_id,
                'name' => $item->product ? $item->product->name : 'منتج محذوف',
                'unit_id' => $item->unit_id,
                'unit_name' => $item->unit ? $item->unit->name : '-',
                'unit_price' => $item->unit_price,
                'original_price' => $originalPrice,
                'is_price_modified' => (float)$item->unit_price !== (float)$originalPrice,
                'quantity' => $item->quantity,
            ];
        }

        // 🌟 حفظ رقم الفاتورة الأصلية لمعالجتها لاحقاً عند الدفع
        $editData = [
            'sale_id' => $sale->id, 
            'customer_id' => $sale->customer_id,
            'paid_cash' => $sale->paid_cash,
            'paid_bankak' => $sale->paid_bankak,
            'transaction_number' => $sale->transaction_number,
        ];

        // ⚠️ لاحظ: نحن لم نعد نحذف الفاتورة هنا! (أمان تام)
        session()->put('pos_cart', $cartToRestore);
        session()->put('pos_edit_data', $editData);

        return redirect()->route('pos')->with('success', 'جاري تعديل الفاتورة.. (سيتم اعتماد التعديل عند تأكيد الدفع)');
    }

    public function render()
    {
        $query = Sale::with('customer');

        if (!empty($this->search_receipt)) {
            $query->where('receipt_number', 'like', '%' . $this->search_receipt . '%')
                  ->orWhereHas('customer', function($q) {
                      $q->where('name', 'like', '%' . $this->search_receipt . '%');
                  });
        }

        if (!empty($this->date_from)) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if (!empty($this->date_to)) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('components.sale-history', [
            'sales' => $sales,
        ])->layout('layouts.app');
    }
}