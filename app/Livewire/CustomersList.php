<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Sale; // للتأكد من المبيعات قبل الحذف

class CustomersList extends Component
{
    public $search = '';

    // متغيرات النافذة المنبثقة (Modal)
    public $isModalOpen = false;
    public $isEditMode = false;
    public $editingCustomerId;

    // حقول العميل
    public $name = '';
    public $phone = '';

    // فتح نافذة إضافة عميل جديد
    public function openModal()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    // إغلاق النافذة
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    // تصفير الحقول
    private function resetFields()
    {
        $this->name = '';
        $this->phone = '';
        $this->editingCustomerId = null;
        $this->resetErrorBag();
    }

    // حفظ العميل (جديد أو تعديل)
    public function saveCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'اسم العميل مطلوب جداً لفتح حساب له.',
        ]);

        if ($this->isEditMode) {
            $customer = Customer::findOrFail($this->editingCustomerId);
            $customer->update([
                'name' => $this->name,
                'phone' => $this->phone,
            ]);
            session()->flash('success', 'تم تحديث بيانات العميل بنجاح! ✅');
        } else {
            Customer::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'balance' => 0, // يبدأ العميل برصيد ديون صفري
            ]);
            session()->flash('success', 'تم تسجيل العميل الجديد بنجاح! ✅');
        }

        $this->closeModal();
    }

    // جلب بيانات العميل لتعديلها
    public function editCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $this->editingCustomerId = $id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    // حذف العميل (مع الحماية المحاسبية)
    public function deleteCustomer($id)
    {
        $customer = Customer::findOrFail($id);

        // 1. التحقق من الديون
        if ($customer->balance > 0) {
            session()->flash('error', '❌ لا يمكن حذف هذا العميل! عليه مديونية بقيمة: ' . number_format($customer->balance, 0));
            return;
        }

        // 2. التحقق من الفواتير السابقة
        $hasSales = Sale::where('customer_id', $id)->exists();
        if ($hasSales) {
            session()->flash('error', '❌ لا يمكن حذف هذا العميل لأنه مرتبط بفواتير مبيعات سابقة. (يمكنك تعديل اسمه إلى "عميل غير نشط" بدلاً من ذلك).');
            return;
        }

        $customer->delete();
        session()->flash('success', 'تم مسح العميل من النظام بنجاح! 🗑️');
    }

    public function render()
    {
        // جلب العملاء مع محرك بحث بسيط وترتيبهم بالأكثر ديناً
        $customers = Customer::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->orderBy('balance', 'desc')
            ->get();
            
        return view('components.customers-list', compact('customers'));
    }
}   