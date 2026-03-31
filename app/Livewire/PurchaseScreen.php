<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PurchaseScreen extends Component
{
    public $supplier_id = '';
    public $purchase_date;
    
    public $selected_product = '';
    public $selected_unit = '';
    public $quantity = '';
    public $unit_cost_price = '';
    public $new_unit_selling_price = '';
    public $expiry_date = ''; 
    
    public $wholesale_prices = []; 
    public $available_units = [];
    public $wholesale_records = [];

    public $cart = [];
    public $total_amount = 0;

    public $paid_cash = 0; 
    public $paid_bankak = 0; 
    public $transaction_number = '';

    public $isSupplierModalOpen = false;
    public $newSupplierName = '';
    public $newSupplierCompany = '';
    public $newSupplierPhone = '';

    public function mount()
    {
        $this->purchase_date = date('Y-m-d');
        
        if (session()->has('purchase_cart')) {
            $this->cart = session('purchase_cart');
            $this->calculateTotal();
        }
    }

    public function openSupplierModal()
    {
        $this->newSupplierName = '';
        $this->newSupplierCompany = '';
        $this->newSupplierPhone = '';
        $this->resetErrorBag();
        $this->isSupplierModalOpen = true;
    }

    public function closeSupplierModal()
    {
        $this->isSupplierModalOpen = false;
    }

    public function saveNewSupplier()
    {
        $this->validate([
            'newSupplierName' => 'required|string|max:255',
            'newSupplierCompany' => 'nullable|string|max:255',
            'newSupplierPhone' => 'nullable|string|max:20',
        ]);

        $supplier = Supplier::create([
            'name' => $this->newSupplierName,
            'company' => $this->newSupplierCompany,
            'phone' => $this->newSupplierPhone,
            'balance' => 0,
        ]);

        $this->supplier_id = $supplier->id;
        $this->closeSupplierModal();
        session()->flash('success_supplier', 'تم إضافة المورد بنجاح واختياره للفاتورة!');
    }

    public function updatedSelectedProduct($productId)
    {
        $this->resetItemFields();
        $this->selected_product = $productId;

        if ($productId) {
            $product = Product::with('baseUnit')->find($productId);
            $this->wholesale_records = ProductUnit::with('unit')->where('product_id', $productId)->get();
            
            $units = collect();
            if ($product->baseUnit) {
                $units->push($product->baseUnit);
            }
            
            foreach ($this->wholesale_records as $record) {
                $units->push($record->unit);
                $this->wholesale_prices[$record->id] = $record->specific_selling_price;
            }
            
            $this->available_units = $units->unique('id')->sortByDesc('conversion_rate')->values()->all();
            
            if (count($this->available_units) > 0) {
                $this->selected_unit = $this->available_units[0]['id'];
                $this->updatedSelectedUnit($this->selected_unit);
            }
        }
    }

    public function updatedSelectedUnit($unitId)
    {
        if ($this->selected_product && $unitId) {
            $product = Product::find($this->selected_product);
            $unit = Unit::find($unitId);
            
            if ($product && $unit) {
                $this->unit_cost_price = round($product->current_cost_price * $unit->conversion_rate, 0);
                $this->new_unit_selling_price = round($product->current_selling_price * $unit->conversion_rate, 0);
            }
        }
    }

    public function addItem()
    {
        $this->validate([
            'selected_product' => 'required',
            'selected_unit' => 'required',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost_price' => 'required|numeric|min:0',
            'new_unit_selling_price' => 'required|numeric|min:0',
            'wholesale_prices.*' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $product = Product::find($this->selected_product);
        $unit = Unit::find($this->selected_unit);
        $subtotal = round($this->quantity * $this->unit_cost_price, 0);
        
        // فحص ذكي لمنع تكرار نفس المنتج وتكرار الإضافة
        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] == $product->id && $item['unit_id'] == $unit->id) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $this->cart[$existingIndex]['quantity'] += $this->quantity;
            $this->cart[$existingIndex]['subtotal'] += $subtotal;
            $this->cart[$existingIndex]['unit_cost_price'] = $this->unit_cost_price;
            $this->cart[$existingIndex]['new_unit_selling_price'] = $this->new_unit_selling_price;
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_id' => $unit->id,
                'unit_name' => $unit->name,
                'conversion_rate' => $unit->conversion_rate,
                'quantity' => $this->quantity,
                'unit_cost_price' => $this->unit_cost_price,
                'new_unit_selling_price' => $this->new_unit_selling_price,
                'expiry_date' => $this->expiry_date ?: null,
                'subtotal' => $subtotal,
                'wholesale_prices' => $this->wholesale_prices, 
            ];
        }

        $this->calculateTotal();
        $this->resetItemFields();
        $this->selected_product = ''; 
    }

    public function removeItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateTotal();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->calculateTotal();
        session()->forget('purchase_cart');
    }

    private function calculateTotal()
    {
        $total = array_sum(array_column($this->cart, 'subtotal'));
        $this->total_amount = round($total, 0);

        $this->paid_cash = $this->total_amount;
        $this->paid_bankak = 0;

        session()->put('purchase_cart', $this->cart);
    }
    
    private function resetItemFields()
    {
        $this->selected_unit = '';
        $this->quantity = '';
        $this->unit_cost_price = '';
        $this->new_unit_selling_price = '';
        $this->expiry_date = ''; 
        $this->wholesale_prices = [];
        $this->available_units = [];
        $this->wholesale_records = [];
    }

    public function savePurchase()
    {
        if (empty($this->cart)) return;

        $total_paid = (float) $this->paid_cash + (float) $this->paid_bankak;
        $remaining = $this->total_amount - $total_paid;

        if ($remaining > 0 && empty($this->supplier_id)) {
            session()->flash('error', 'لا يمكن تسجيل فاتورة آجلة (بها ديون) بدون اختيار المورد!');
            return;
        }
        
        $status = 'paid';
        if ($remaining == $this->total_amount) $status = 'unpaid';
        elseif ($remaining > 0) $status = 'partial';

        $method = 'cash';
        if ($this->paid_cash > 0 && $this->paid_bankak > 0) $method = 'split';
        elseif ($this->paid_bankak > 0) $method = 'bankak';

        DB::transaction(function () use ($total_paid, $remaining, $status, $method) {
            
            $supplierName = null;
            if (!empty($this->supplier_id)) {
                $supplier = Supplier::find($this->supplier_id);
                $supplierName = $supplier ? $supplier->name : null;
                
                if ($remaining > 0 && $supplier) {
                    $supplier->increment('balance', $remaining);
                }
            }

            $purchase = Purchase::create([
                'supplier_id' => empty($this->supplier_id) ? null : $this->supplier_id,
                'supplier_name' => $supplierName, 
                'purchase_date' => $this->purchase_date ?: date('Y-m-d'),
                'total_amount' => $this->total_amount,
                'paid_amount' => $total_paid,
                'remaining_amount' => $remaining, 
                'paid_cash' => (float) $this->paid_cash,
                'paid_bankak' => (float) $this->paid_bankak,
                'payment_method' => $method,
                'transaction_number' => $this->transaction_number,
                'payment_status' => $status,
            ]);

            foreach ($this->cart as $item) {
                // بمجرد تنفيذ هذا السطر، سيستيقظ الـ Observer ليقوم بتحديث المخزون والأسعار الأساسية تلقائياً
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost_price' => $item['unit_cost_price'],
                    'new_unit_selling_price' => $item['new_unit_selling_price'],
                    'expiry_date' => $item['expiry_date'], 
                ]);

                // 🌟 هنا نقوم فقط بتحديث تسعيرات الجملة المخصصة (لأن الـ Observer لا يعرف عنها شيئاً)
                if (!empty($item['wholesale_prices'])) {
                    foreach ($item['wholesale_prices'] as $recordId => $newPrice) {
                        ProductUnit::where('id', $recordId)->update([
                            'specific_selling_price' => $newPrice !== '' ? $newPrice : null
                        ]);
                    }
                }
            }
        });

        $this->clearCart();
        $this->supplier_id = '';
        $this->transaction_number = '';
        $this->resetItemFields();
        $this->selected_product = '';
        $this->cart = [];
        $this->total_amount = 0;

        return redirect()->route('purchases.history')->with('success', '✅ تم حفظ فاتورة المشتريات وتحديث أسعار المخزن بنجاح!');
    }

    public function render()
    {
        return view('components.purchase-screen', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }
}