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

    public $isCustomerModalOpen = false;
    public $newCustomerName = '';
    public $newCustomerPhone = '';

    public $heldInvoices = [];
    public $isHeldInvoicesModalOpen = false;

    public $editing_sale_id = null; // 🌟 متغير جديد لمعرفة هل نحن في وضع التعديل

    public $is_manual_payment = false; // 🌟 متغير جديد لمعرفة هل الكاشير كتب المبلغ بيده أم لا

    public function mount()
    {
        if (session()->has('pos_edit_data')) {
            $editData = session('pos_edit_data');
            $this->editing_sale_id = $editData['sale_id'] ?? null;
            $this->customer_id = $editData['customer_id'] ?? '';
            $this->paid_cash = $editData['paid_cash'] ?? 0;
            $this->paid_bankak = $editData['paid_bankak'] ?? 0;
            $this->transaction_number = $editData['transaction_number'] ?? '';
            
            $this->is_manual_payment = true;
            // ⚠️ أزلنا كود (نسيان السيشن) من هنا لكي لا يضيع عند الـ Refresh!
        }

        if (session()->has('pos_cart')) {
            $this->cart = session('pos_cart');
            $this->calculateTotal();
        }
        
        $this->heldInvoices = session()->get('held_invoices', []);
    }

    // 🌟 دوال لحفظ أي تعديل يجريه الكاشير على المبالغ حتى لا تضيع مع الـ Refresh
    public function updatedPaidCash() { $this->is_manual_payment = true; $this->updateSessionEditData(); }
    public function updatedPaidBankak() { $this->is_manual_payment = true; $this->updateSessionEditData(); }
    public function updatedCustomerId() { $this->updateSessionEditData(); }
    public function updatedTransactionNumber() { $this->updateSessionEditData(); }


    private function updateSessionEditData() {
        if ($this->editing_sale_id) {
            session()->put('pos_edit_data', [
                'sale_id' => $this->editing_sale_id,
                'customer_id' => $this->customer_id,
                'paid_cash' => $this->paid_cash,
                'paid_bankak' => $this->paid_bankak,
                'transaction_number' => $this->transaction_number,
            ]);
        }
    }


    public function holdInvoice()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'السلة فارغة، لا يوجد شيء لتعليقه!');
            return;
        }

        $invoiceData = [
            'cart' => $this->cart,
            'customer_id' => $this->customer_id,
            'paid_cash' => $this->paid_cash,
            'paid_bankak' => $this->paid_bankak,
            'total' => $this->total,
            'time' => now()->format('H:i:s'),
        ];

        $this->heldInvoices[] = $invoiceData;
        session()->put('held_invoices', $this->heldInvoices);

        $this->clearCart();
        $this->customer_id = '';
        $this->paid_cash = 0;
        $this->paid_bankak = 0;

        session()->flash('success', 'تم تعليق الفاتورة بنجاح. يمكنك خدمة العميل التالي!');
    }

    public function restoreInvoice($index)
    {
        if (isset($this->heldInvoices[$index])) {
            $invoiceToRestore = $this->heldInvoices[$index];

            unset($this->heldInvoices[$index]);
            $this->heldInvoices = array_values($this->heldInvoices);
            session()->put('held_invoices', $this->heldInvoices);

            if (!empty($this->cart)) {
                $this->holdInvoice();
            }

            $this->cart = $invoiceToRestore['cart'];
            $this->customer_id = $invoiceToRestore['customer_id'] ?? '';
            $this->paid_cash = $invoiceToRestore['paid_cash'] ?? 0;
            $this->paid_bankak = $invoiceToRestore['paid_bankak'] ?? 0;
           
            $this->calculateTotal();
            $this->isHeldInvoicesModalOpen = false;
           
            session()->flash('success', 'تم استرجاع الفاتورة المعلقة بنجاح!');
        }
    }

    public function deleteHeldInvoice($index)
    {
        if (isset($this->heldInvoices[$index])) {
            unset($this->heldInvoices[$index]);
            $this->heldInvoices = array_values($this->heldInvoices);
            session()->put('held_invoices', $this->heldInvoices);
        }
    }

    public function toggleHeldInvoicesModal()
    {
        $this->isHeldInvoicesModalOpen = !$this->isHeldInvoicesModalOpen;
    }

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
           
            // 🌟 السحر هنا: فصل المنتج من مكانه الحالي وإعادته في قمة المصفوفة
            $item = $this->cart[$cartKey];
            unset($this->cart[$cartKey]);
            $this->cart = [$cartKey => $item] + $this->cart;
           
        } else {
            $unitPrice = $product->getPriceForUnit($unitId);
            $newItem = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_id' => $unitId,
                'unit_name' => $unit->name,
                'unit_price' => $unitPrice,
                'original_price' => $unitPrice,
                'is_price_modified' => false,
                'quantity' => $quantity,
            ];
           
            // 🌟 إدراج المنتج الجديد في بداية المصفوفة (أعلى الفاتورة)
            $this->cart = [$cartKey => $newItem] + $this->cart;
        }
       
        $this->calculateTotal();

        // 🌟 إرسال حدث (Event) للواجهة الأمامية لتشغيل الصوت وإظهار الإشعار
        $this->dispatch('item-added', productName: $product->name);
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
           
            // 🌟 اكتشاف التغيير: هل السعر المدخل يختلف عن السعر الأصلي المسجل بالداتابيز؟
            $originalPrice = $this->cart[$key]['original_price'] ?? 0;
            if ((float)$newPrice !== (float)$originalPrice) {
                $this->cart[$key]['is_price_modified'] = true;
            } else {
                $this->cart[$key]['is_price_modified'] = false;
            }
        }
        $this->calculateTotal();
    }

    // ==========================================
    // 🌟 دالة السحر: اعتماد السعر الجديد في النظام 🌟
    // ==========================================
    public function saveNewOfficialPrice($key)
    {
        if (!isset($this->cart[$key])) return;

        $item = $this->cart[$key];
        $productId = $item['product_id'];
        $unitId = $item['unit_id'];
        $newPrice = $item['unit_price'];

        $product = Product::find($productId);
        $unit = Unit::find($unitId);

        if ($product && $unit) {
            // تحديث السعر في قاعدة البيانات
            if ($product->base_unit_id == $unitId) {
                // إذا كانت الوحدة هي الأساسية (قطاعي)، نحدث السعر الأساسي
                $basePrice = $newPrice / $unit->conversion_rate;
                $product->update(['current_selling_price' => $basePrice]);
            } else {
                // إذا كانت الوحدة فرعية (جملة)، نحدث تسعيرة الجملة
                ProductUnit::where('product_id', $productId)
                    ->where('unit_id', $unitId)
                    ->update(['specific_selling_price' => $newPrice]);
            }

            // تحديث بيانات السلة لكي يختفي الزر الأخضر
            $this->cart[$key]['original_price'] = $newPrice;
            $this->cart[$key]['is_price_modified'] = false;
            session()->put('pos_cart', $this->cart);

            session()->flash('success', '✅ تم اعتماد وتحديث السعر رسمياً في النظام!');
        }
    }
    // ==========================================

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

        // 🌟 السحر هنا: إذا لم يتدخل الكاشير يدوياً ولم تكن فاتورة مسترجعة، اجعل الكاش = الإجمالي
        if (!$this->is_manual_payment) {
            $this->paid_cash = (int) $this->total;
            $this->paid_bankak = 0;
        }

        session()->put('pos_cart', $this->cart);
    }

    public function removeFromCart($key)
    {
        unset($this->cart[$key]);
        $this->calculateTotal();
    }
   
    public function clearCart()
    {
        $this->cart = [];
        $this->editing_sale_id = null; // تصفير وضع التعديل
        $this->is_manual_payment = false;
        $this->customer_id = '';
        $this->paid_cash = 0;
        $this->paid_bankak = 0;
        $this->transaction_number = '';
        $this->calculateTotal();
        
        session()->forget('pos_cart'); 
        session()->forget('pos_edit_data'); // تنظيف السيشن
    }

    public function checkout()
    {
        if (empty($this->cart)) return;

        $total_paid = (float) $this->paid_cash + (float) $this->paid_bankak;
        $remaining_amount = $this->total - $total_paid;

        if ($remaining_amount > 0 && empty($this->customer_id)) {
            session()->flash('error', 'لا يمكن تسجيل فاتورة آجلة بدون اختيار العميل!');
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
            
            // 🚨 الخطوة 1: إذا كنا نعدل فاتورة قديمة، نقوم بإلغائها وعكسها (بشكل آمن)
            if ($this->editing_sale_id) {
                $oldSale = Sale::with('items')->find($this->editing_sale_id);
                if ($oldSale) {
                    // عكس ديون العميل
                    if ($oldSale->customer_id && $oldSale->remaining_amount > 0) {
                        $oldCustomer = Customer::find($oldSale->customer_id);
                        if ($oldCustomer) {
                            $oldCustomer->decrement('balance', $oldSale->remaining_amount);
                        }
                    }
                    // إرجاع البضاعة للمخزن
                    foreach ($oldSale->items as $item) {
                        $item->delete(); 
                    }
                    // مسح الفاتورة القديمة
                    $oldSale->delete();
                }
            }

            // 🚨 الخطوة 2: إنشاء الفاتورة الجديدة وحفظها
            $sale = Sale::create([
                'user_id' => auth()->id(), 
                'receipt_number' => 'INV-' . time(),
                'total_amount' => $this->total,
                'customer_id' => empty($this->customer_id) ? null : $this->customer_id,
                'paid_amount' => $total_paid,
                'remaining_amount' => $remaining_amount, 
                'paid_cash' => (float) $this->paid_cash,       
                'paid_bankak' => (float) $this->paid_bankak,   
                'payment_method' => $method,                   
                'transaction_number' => $this->transaction_number,
                'payment_status' => $payment_status,
                'type' => 'retail',
            ]);

            $saleId = $sale->id;

            foreach ($this->cart as $item) {
                $product = Product::find($item['product_id']);
                $unit = Unit::find($item['unit_id']);
                
                $conversionRate = $unit ? $unit->conversion_rate : 1;
                $itemCostPrice = ($product ? $product->current_cost_price : 0) * $conversionRate;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'cost_price' => $itemCostPrice, 
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            // إضافة الدين للعميل الجديد
            if ($remaining_amount > 0 && !empty($this->customer_id)) {
                $customer = Customer::find($this->customer_id);
                if ($customer) {
                    $customer->increment('balance', $remaining_amount);
                }
            }
        });

        // تنظيف الشاشة بعد الدفع
        $this->clearCart();
        
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
            $searchTerms = explode(' ', trim($this->search));
           
            $productsQuery->where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $cleanTerm = trim($term);
                   
                    if ($cleanTerm !== '') {
                        $normalizedTerm = str_replace(['أ', 'إ', 'آ', 'ا'], '_', $cleanTerm);
                        $normalizedTerm = str_replace(['ة', 'ه'], '_', $normalizedTerm);      
                        $normalizedTerm = str_replace(['ي', 'ى', 'ئ'], '_', $normalizedTerm);
                       
                        $query->where('name', 'like', '%' . $normalizedTerm . '%');
                    }
                }
            });
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
