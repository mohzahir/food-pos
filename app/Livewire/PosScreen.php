<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Unit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class PosScreen extends Component
{
    public $cart = []; 
    public $barcode = ''; 
    public $search = ''; 
    public $total = 0; 
    
    public $customer_id = ''; 
    public $payment_method = 'cash'; 
    public $paid_cash = 0; 
    public $paid_bankak = 0; 
    public $transaction_number = '';
    public $selected_category = null;

    // --- متغيرات إضافة عميل سريع ---
    public $isCustomerModalOpen = false;
    public $newCustomerName = '';
    public $newCustomerPhone = '';

    // دالة تعمل عند تحميل الشاشة لأول مرة
    public function mount()
    {
        // استرجاع السلة من الـ Session إذا كانت موجودة
        if (session()->has('pos_cart')) {
            $this->cart = session('pos_cart');
            $this->calculateTotal();
        }
    }

    // --- دوال العميل السريع ---
    public function openCustomerModal()
    {
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
        $this->resetErrorBag();
        $this->isCustomerModalOpen = true;
    }

    public function closeCustomerModal()
    {
        $this->isCustomerModalOpen = false;
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create([
            'name' => $this->newCustomerName,
            'phone' => $this->newCustomerPhone,
            'balance' => 0,
        ]);

        // اختيار العميل الجديد تلقائياً في الفاتورة الحالية
        $this->customer_id = $customer->id;
        $this->closeCustomerModal();
        session()->flash('success_customer', 'تم إضافة العميل بنجاح واختياره للفاتورة!');
    }


    public function selectCategory($categoryId)
    {
        $this->selected_category = $categoryId;
    }

    public function updatedBarcode()
    {
        if (empty($this->barcode)) return;
        $barcodeStr = trim($this->barcode);

        if (strlen($barcodeStr) === 13 && str_starts_with($barcodeStr, '20')) {
            $skuFromScale = (int) substr($barcodeStr, 2, 5); 
            $weightInGrams = (float) substr($barcodeStr, 7, 5);
            $weightInKg = $weightInGrams / 1000; 
            $product = Product::where('sku', (string) $skuFromScale)->first();

            if ($product) {
                $this->addToCart($product->id, $product->base_unit_id, $weightInKg);
            } else {
                session()->flash('error', 'صنف الميزان غير معرف (SKU: ' . $skuFromScale . ')');
            }
            $this->barcode = '';
            return; 
        }

        $productUnit = ProductUnit::where('barcode', $barcodeStr)->first();

        if ($productUnit) {
            $this->addToCart($productUnit->product_id, $productUnit->unit_id);
        } else {
            $product = Product::where('sku', $barcodeStr)->first();
            if ($product) {
                $this->addToCart($product->id, $product->base_unit_id);
            } else {
                session()->flash('error', 'المنتج غير موجود!');
            }
        }
        $this->barcode = ''; 
    }

    public function addToCart($productId, $unitId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);
        $unit = Unit::find($unitId);
        $cartKey = $productId . '_' . $unitId; 

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity']++;
        } else {
            $unitPrice = $product->getPriceForUnit($unitId); 
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_id' => $unitId,
                'unit_name' => $unit->name,
                'unit_price' => $unitPrice,
                'quantity' => $quantity, 
            ];
        }
        $this->calculateTotal();
    }

    public function updateQuantity($key, $quantity)
    {
        if (is_numeric($quantity) && $quantity > 0) {
            $this->cart[$key]['quantity'] = (float) $quantity;
        } else {
            $this->cart[$key]['quantity'] = 1;
        }
        $this->calculateTotal();
    }

    public function updatePrice($key, $newPrice)
    {
        if (is_numeric($newPrice) && $newPrice >= 0) {
            $this->cart[$key]['unit_price'] = (float) $newPrice;
        }
        $this->calculateTotal();
    }

    public function updateSubtotal($key, $newSubtotal)
    {
        if (is_numeric($newSubtotal) && $newSubtotal >= 0) {
            $unitPrice = $this->cart[$key]['unit_price'];
            if ($unitPrice > 0) {
                $calculatedQuantity = $newSubtotal / $unitPrice;
                $this->cart[$key]['quantity'] = round($calculatedQuantity, 4);
            }
        }
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = array_sum(array_map(function ($item) {
            return round($item['unit_price'] * $item['quantity'], 0);
        }, $this->cart));

        $this->paid_cash = (int) $this->total; 
        $this->paid_bankak = 0;

        // --- حفظ السلة في الـ Session كلما تغير الإجمالي ---
        session()->put('pos_cart', $this->cart);
    }

    public function removeFromCart($key)
    {
        unset($this->cart[$key]);
        $this->calculateTotal();
    }
    
    // مسح السلة يدوياً
    public function clearCart()
    {
        $this->cart = [];
        $this->calculateTotal();
        session()->forget('pos_cart'); // مسح من الـ Session
    }

    public function checkout()
    {
        if (empty($this->cart)) return;

        $total_paid = (float) $this->paid_cash + (float) $this->paid_bankak;
        $remaining_amount = $this->total - $total_paid;

        if ($remaining_amount > 0 && empty($this->customer_id)) {
            session()->flash('error', 'لا يمكن تسجيل فاتورة آجلة (بها متبقي) بدون اختيار العميل!');
            return;
        }

        $payment_status = 'paid';
        if ($remaining_amount == $this->total) $payment_status = 'unpaid';
        elseif ($remaining_amount > 0) $payment_status = 'partial';

        $method = 'cash';
        if ($this->paid_cash > 0 && $this->paid_bankak > 0) $method = 'split'; 
        elseif ($this->paid_bankak > 0) $method = 'bankak';

        $saleId = null;

        DB::transaction(function () use (&$saleId, $remaining_amount, $payment_status, $total_paid, $method) {
            
            $sale = Sale::create([
                'receipt_number' => 'INV-' . time(),
                'total_amount' => $this->total,
                'customer_id' => empty($this->customer_id) ? null : $this->customer_id,
                'paid_amount' => $total_paid,
                'paid_cash' => (float) $this->paid_cash,       
                'paid_bankak' => (float) $this->paid_bankak,   
                'payment_method' => $method,                   
                'transaction_number' => $this->transaction_number,
                'payment_status' => $payment_status,
                'type' => 'retail',
            ]);

            $saleId = $sale->id;

            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            if ($remaining_amount > 0 && !empty($this->customer_id)) {
                $customer = Customer::find($this->customer_id);
                if ($customer) {
                    $customer->increment('balance', $remaining_amount);
                }
            }
        });

        // --- مسح السلة بعد إتمام البيع ---
        $this->cart = [];
        $this->total = 0;
        $this->customer_id = '';
        $this->payment_method = 'cash';
        $this->paid_amount = 0;
        $this->transaction_number = '';
        session()->forget('pos_cart'); // مسح من الـ Session
        
        return redirect()->route('receipt.show', $saleId);
    }

    public function render()
    {
        $customers = Customer::orderBy('name')->get();
        $categories = Category::all();

        $productsQuery = Product::where('is_active', true)->with(['baseUnit', 'productUnits.unit']);
        
        if ($this->selected_category) {
            $productsQuery->where('category_id', $this->selected_category);
        }

        if (!empty($this->search)) {
            $productsQuery->where('name', 'like', '%' . $this->search . '%');
        }
        
        $products = $productsQuery->get();
        $quickItems = collect();

        foreach ($products as $product) {
            if ($product->baseUnit) {
                $quickItems->push([
                    'product_id' => $product->id,
                    'unit_id' => $product->base_unit_id,
                    'name' => $product->name,
                    'unit_name' => $product->baseUnit->name,
                    'price' => $product->current_selling_price,
                    'is_wholesale' => false,
                ]);
            }

            foreach ($product->productUnits as $pu) {
                if ($pu->unit) {
                    $quickItems->push([
                        'product_id' => $product->id,
                        'unit_id' => $pu->unit_id,
                        'name' => $product->name,
                        'unit_name' => $pu->unit->name,
                        'price' => $pu->specific_selling_price ?? ($product->current_selling_price * $pu->unit->conversion_rate),
                        'is_wholesale' => true,
                    ]);
                }
            }
        }

        return view('components.pos-screen', compact('customers', 'categories', 'quickItems'));
    }
}