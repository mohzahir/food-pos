<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\InventoryTransaction;

class InventoryScreen extends Component
{
    public $search = '';

    // --- متغيرات تسوية المخزون ---
    public $isReconciliationModalOpen = false;
    public $editingProduct = null;
    public $actual_stock = '';
    public $reconciliation_reason = 'جرد دوري'; // السبب الافتراضي

    // دالة فتح نافذة التسوية لمنتج معين
    public function openReconciliation($productId)
    {
        $this->editingProduct = Product::find($productId);
        $this->actual_stock = (float) $this->editingProduct->current_stock;
        $this->reconciliation_reason = 'جرد دوري';
        $this->isReconciliationModalOpen = true;
    }

    // دالة إغلاق النافذة
    public function closeModal()
    {
        $this->isReconciliationModalOpen = false;
        $this->editingProduct = null;
    }

    // دالة حفظ التسوية
    // لا تنسَ استدعاء الموديل الجديد في أعلى الملف

    public function saveReconciliation()
    {
        $this->validate([
            'actual_stock' => 'required|numeric|min:0',
            'reconciliation_reason' => 'required|string|max:255',
        ]);

        if ($this->editingProduct) {
            $oldStock = (float) $this->editingProduct->current_stock;
            $newStock = (float) $this->actual_stock;
            $variance = $newStock - $oldStock; // الفارق (الزيادة أو العجز)

            // 🌟 تسجيل الحركة فقط إذا كان هناك تغيير فعلي في الرصيد
            if ($variance != 0) {
                
                // 1. تسجيل الحركة في دفتر المراقبة السري (Transactions)
                \App\Models\InventoryTransaction::create([
                    'product_id' => $this->editingProduct->id,
                    'user_id' => auth()->id(), // من هو الموظف الحالي؟
                    'type' => 'reconciliation', // نوع الحركة: تسوية جردية
                    'quantity' => $variance, // سيسجل بالناقص إذا كان عجزاً، وبالموجب إذا كانت زيادة
                    'balance_before' => $oldStock,
                    'balance_after' => $newStock,

                    // 🌟 القطعة الناقصة: حفظ التكلفة وإجمالي القيمة المالية للحركة
                    'unit_cost' => $this->editingProduct->current_cost_price,
                    'total_value' => $variance * $this->editingProduct->current_cost_price,
                    
                    'notes' => $this->reconciliation_reason,
                ]);

                // 2. تحديث الرصيد الفعلي للمنتج
                $this->editingProduct->update([
                    'current_stock' => $newStock
                ]);

                session()->flash('success', "تمت تسوية مخزون ({$this->editingProduct->name}) بنجاح! الفارق: {$variance}");
            } else {
                session()->flash('success', "لم يتم إجراء أي تعديل، الرصيد الفعلي يطابق الدفتري.");
            }

            $this->closeModal();
        }
    }

    public function render()
    {
        $products = Product::with(['baseUnit', 'productUnits.unit'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('sku', 'like', '%' . $this->search . '%')
            ->orderBy('current_stock', 'asc')
            ->get();

        $totalInventoryCost = 0; 
        $totalExpectedRevenue = 0; 

        foreach ($products as $p) {
            $totalInventoryCost += ($p->current_stock * $p->current_cost_price);
            $totalExpectedRevenue += ($p->current_stock * $p->current_selling_price);
        }

        $expectedProfit = $totalExpectedRevenue - $totalInventoryCost;

        return view('components.inventory-screen', [
            'products' => $products,
            'totalInventoryCost' => $totalInventoryCost,
            'totalExpectedRevenue' => $totalExpectedRevenue,
            'expectedProfit' => $expectedProfit,
        ]);
    }
}