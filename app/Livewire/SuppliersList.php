<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Purchase; // للتحقق من الفواتير قبل الحذف

class SuppliersList extends Component
{
    public $search = '';

    // متغيرات النافذة المنبثقة
    public $isModalOpen = false;
    public $isEditMode = false;
    public $editingSupplierId;

    // حقول المورد
    public $name = '';
    public $company = '';
    public $phone = '';

    public function openModal()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->company = '';
        $this->phone = '';
        $this->editingSupplierId = null;
        $this->resetErrorBag();
    }

    public function saveSupplier()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'اسم المورد أو المندوب مطلوب.',
        ]);

        if ($this->isEditMode) {
            $supplier = Supplier::findOrFail($this->editingSupplierId);
            $supplier->update([
                'name' => $this->name,
                'company' => $this->company,
                'phone' => $this->phone,
            ]);
            session()->flash('success', 'تم تحديث بيانات المورد بنجاح! ✅');
        } else {
            Supplier::create([
                'name' => $this->name,
                'company' => $this->company,
                'phone' => $this->phone,
                'balance' => 0, // يبدأ المورد برصيد ديون صفري
            ]);
            session()->flash('success', 'تم تسجيل المورد الجديد بنجاح! ✅');
        }

        $this->closeModal();
    }

    public function editSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->editingSupplierId = $id;
        $this->name = $supplier->name;
        $this->company = $supplier->company;
        $this->phone = $supplier->phone;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);

        // 1. التحقق من الديون (التي علينا)
        if ($supplier->balance > 0) {
            session()->flash('error', '❌ لا يمكن حذف هذا المورد! له مديونية مستحقة علينا بقيمة: ' . number_format($supplier->balance, 0));
            return;
        }

        // 2. التحقق من الفواتير السابقة
        $hasPurchases = Purchase::where('supplier_id', $id)->exists();
        if ($hasPurchases) {
            session()->flash('error', '❌ لا يمكن حذف هذا المورد لارتباطه بفواتير مشتريات سابقة. (يفضل تعديل اسمه إلى "مورد غير نشط").');
            return;
        }

        $supplier->delete();
        session()->flash('success', 'تم مسح المورد من النظام بنجاح! 🗑️');
    }

    public function render()
    {
        // جلب الموردين مع البحث والترتيب حسب الديون الأعلى
        $suppliers = Supplier::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('company', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->orderBy('balance', 'desc')
            ->get();
            
        return view('components.suppliers-list', compact('suppliers'))->layout('layouts.app');
    }
}