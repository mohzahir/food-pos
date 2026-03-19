<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination; 
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;
use App\Models\Category;

class ProductManager extends Component
{
    use WithPagination;

    // متغيرات إضافة الفئة
    public $category_name;

    // متغيرات الوحدات
    public $unit_name, $unit_type = 'quantity', $conversion_rate = 1;
    
    // متغيرات إضافة المنتج
    public $product_name, $sku, $base_unit_id, $stock_unit_id, $cost_price, $selling_price, $initial_stock = 0, $category_id = '';
    
    // متغيرات تسعير الجملة
    public $selected_product, $selected_unit, $barcode;
    public $specific_selling_price = ''; 

    // متغيرات نافذة التعديل (Edit Modal) القطاعي
    public $isEditModalOpen = false;
    public $editing_product_id;
    public $edit_product_name, $edit_sku, $edit_cost_price, $edit_selling_price, $edit_category_id = '';
    public $edit_base_unit_name;

    // 🌟 متغير البحث في المنتجات القطاعي
    public $search_product = '';

    // 🌟 متغيرات إدارة منتجات الجملة
    public $search_wholesale = '';
    
    // متغيرات نافذة تعديل الجملة
    public $isEditWholesaleModalOpen = false;
    public $editing_wholesale_id;
    public $edit_wholesale_price;
    public $edit_wholesale_barcode;
    public $edit_wholesale_product_name; // للعرض فقط في النافذة


    public $expiry_date = '';

    
    // تصفير صفحات الجملة عند البحث
    public function updatingSearchWholesale()
    {
        $this->resetPage('wholesalePage');
    }

    // تصفير صفحات المنتجات عند البحث
    public function updatingSearchProduct()
    {
        $this->resetPage();
    }

    // ==========================================
    // --- دوال نافذة تعديل الجملة ---
    // ==========================================
    public function editWholesale($id)
    {
        $pu = ProductUnit::with(['product', 'unit'])->findOrFail($id);
        
        $this->editing_wholesale_id = $pu->id;
        $this->edit_wholesale_price = $pu->specific_selling_price;
        $this->edit_wholesale_barcode = $pu->barcode;
        $this->edit_wholesale_product_name = $pu->product->name . ' (' . $pu->unit->name . ')';
        
        $this->isEditWholesaleModalOpen = true;
    }

    public function updateWholesale()
    {
        $this->validate([
            'edit_wholesale_barcode' => 'required|unique:product_units,barcode,' . $this->editing_wholesale_id,
            'edit_wholesale_price' => 'nullable|numeric|min:0',
        ], [
            'edit_wholesale_barcode.unique' => 'هذا الباركود مستخدم مسبقاً لمنتج جملة آخر!',
        ]);

        ProductUnit::findOrFail($this->editing_wholesale_id)->update([
            'barcode' => $this->edit_wholesale_barcode,
            'specific_selling_price' => $this->edit_wholesale_price ?: null,
        ]);

        session()->flash('barcode_success', 'تم تحديث تسعيرة الجملة والباركود بنجاح!');
        $this->isEditWholesaleModalOpen = false;
    }

    public function closeWholesaleModal()
    {
        $this->isEditWholesaleModalOpen = false;
    }

    // ==========================================
    // --- دوال الفئات ---
    // ==========================================
    public function addCategory()
    {
        $this->validate(['category_name' => 'required|string|unique:categories,name']);
        Category::create(['name' => $this->category_name]);
        session()->flash('category_success', 'تمت إضافة الفئة بنجاح!');
        $this->reset(['category_name']);
    }

    public function deleteCategory($id)
    {
        $hasProducts = Product::where('category_id', $id)->exists();
        if ($hasProducts) {
            session()->flash('category_error', '❌ لا يمكن حذف الفئة لوجود منتجات بداخلها!');
            return;
        }
        Category::findOrFail($id)->delete();
        session()->flash('category_success', 'تم حذف الفئة بنجاح.');
    }

    // ==========================================
    // --- دوال الوحدة ---
    // ==========================================
    public function addUnit()
    {
        $this->validate([
            'unit_name' => 'required|string|unique:units,name',
            'conversion_rate' => 'required|numeric|min:1',
        ]);
        Unit::create(['name' => $this->unit_name, 'type' => $this->unit_type, 'conversion_rate' => $this->conversion_rate]);
        session()->flash('unit_success', 'تمت إضافة الوحدة بنجاح!');
        $this->reset(['unit_name', 'conversion_rate']);
    }

    public function deleteUnit($id)
    {
        $used = Product::where('base_unit_id', $id)->exists() || ProductUnit::where('unit_id', $id)->exists() || \App\Models\SaleItem::where('unit_id', $id)->exists() || \App\Models\PurchaseItem::where('unit_id', $id)->exists();
        if ($used) {
            session()->flash('unit_error', '❌ لا يمكن حذف الوحدة لأنها مستخدمة!');
            return;
        }
        Unit::findOrFail($id)->delete();
        session()->flash('unit_success', 'تم حذف الوحدة بنجاح.');
    }

    // ==========================================
    // --- دوال المنتجات الأساسية ---
    // ==========================================
    public function addProduct()
    {
        $this->validate([
            'product_name' => 'required|string',
            'sku' => 'nullable|unique:products,sku',
            'base_unit_id' => 'required',
            'stock_unit_id' => 'required',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'initial_stock' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date', // 🌟 إضافة التحقق
        ]);

        $stockUnit = Unit::findOrFail($this->stock_unit_id);
        $actualStock = (float)$this->initial_stock * $stockUnit->conversion_rate;

        Product::create([
            'name' => $this->product_name,
            'sku' => $this->sku ?: (string) rand(10000000, 99999999),
            'category_id' => $this->category_id ?: null,
            'base_unit_id' => $this->base_unit_id,
            'current_cost_price' => $this->cost_price, 
            'current_selling_price' => $this->selling_price,
            'expiry_date' => $this->expiry_date ?: null, // 🌟 حفظ التاريخ
            'current_stock' => $actualStock,
            'has_fraction' => true,
            'is_active' => true,
        ]);

        session()->flash('product_success', 'تمت إضافة المنتج بنجاح!');
        $this->reset(['product_name', 'sku', 'category_id', 'base_unit_id', 'stock_unit_id', 'cost_price', 'selling_price', 'initial_stock', 'expiry_date']);    }

    public function toggleProductStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
    }

    public function deleteProduct($id)
    {
        $hasTrans = \App\Models\SaleItem::where('product_id', $id)->exists() || \App\Models\PurchaseItem::where('product_id', $id)->exists();
        if ($hasTrans) {
            session()->flash('product_error', '❌ لا يمكن حذف المنتج لارتباطه بفواتير.');
            return;
        }
        \App\Models\ProductUnit::where('product_id', $id)->delete();
        Product::findOrFail($id)->delete();
        session()->flash('product_success', 'تم الحذف بنجاح.');
    }

    // ==========================================
    // --- دوال نافذة تعديل المنتج (قطاعي) ---
    // ==========================================
    public function editProduct($id)
    {
        $product = Product::with('baseUnit')->findOrFail($id);
        $this->editing_product_id = $product->id;
        $this->edit_product_name = $product->name;
        $this->edit_sku = $product->sku;
        $this->edit_category_id = $product->category_id;
        $this->edit_cost_price = $product->current_cost_price;
        $this->edit_selling_price = $product->current_selling_price;
        $this->isEditModalOpen = true; 
    }

    public function updateProduct()
    {
        $this->validate([
            'edit_product_name' => 'required|string',
            'edit_sku' => 'required|unique:products,sku,' . $this->editing_product_id,
            'edit_cost_price' => 'required|numeric|min:0',
            'edit_selling_price' => 'required|numeric|min:0',
        ]);

        Product::findOrFail($this->editing_product_id)->update([
            'name' => $this->edit_product_name,
            'sku' => $this->edit_sku,
            'category_id' => $this->edit_category_id ?: null,
            'current_cost_price' => $this->edit_cost_price,
            'current_selling_price' => $this->edit_selling_price,
        ]);

        session()->flash('product_success', 'تم تحديث المنتج!');
        $this->isEditModalOpen = false;
    }

    public function closeModal()
    {
        $this->isEditModalOpen = false;
    }

    // ==========================================
    // --- دوال تسعير الجملة الجديدة ---
    // ==========================================
    public function addBarcode()
    {
        $this->validate(['selected_product' => 'required', 'selected_unit' => 'required']);
        ProductUnit::create([
            'product_id' => $this->selected_product,
            'unit_id' => $this->selected_unit,
            'barcode' => $this->barcode ?: (string) rand(10000000, 99999999),
            'specific_selling_price' => $this->specific_selling_price ?: null, 
        ]);
        session()->flash('barcode_success', 'تم ربط الباركود وتحديد سعر الجملة!');
        $this->reset(['selected_product', 'selected_unit', 'barcode', 'specific_selling_price']);
    }
    
    public function deleteBarcode($id)
    {
        ProductUnit::findOrFail($id)->delete();
    }

    // ==========================================
    // --- الرندر (تجهيز البيانات للواجهة) ---
    // ==========================================
    public function render()
    {
        // 1. جلب المنتجات الأساسية (قطاعي) بنظام الصفحات والبحث
        $productsList = Product::with(['baseUnit', 'category'])
            ->where('name', 'like', '%' . $this->search_product . '%')
            ->orWhere('sku', 'like', '%' . $this->search_product . '%')
            ->orderBy('id', 'desc')
            ->paginate(15); 

        // 2. 🌟 جلب منتجات الجملة بنظام بحث وصفحات (مستقل)
        $productUnitsQuery = ProductUnit::with(['product', 'unit']);
        if (!empty($this->search_wholesale)) {
            $productUnitsQuery->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search_wholesale . '%');
            })->orWhere('barcode', 'like', '%' . $this->search_wholesale . '%');
        }
        // استخدام 'wholesalePage' كاسم لصفحات هذا الجدول لمنع التداخل
        $productUnitsList = $productUnitsQuery->latest()->paginate(10, ['*'], 'wholesalePage'); 

        return view('components.product-manager', [
            'categoriesList' => Category::orderBy('name')->get(),
            'baseUnitsList' => Unit::where('conversion_rate', 1)->get(),
            'wholesaleUnitsList' => Unit::where('conversion_rate', '>', 1)->get(),
            'allUnitsList' => Unit::orderBy('conversion_rate')->get(),
            
            'productsList' => $productsList,
            'productUnitsList' => $productUnitsList, // تمرير المتغير الجديد للواجهة
            
            // قائمة مبسطة لتغذية القائمة المنسدلة في نموذج "إضافة تسعيرة جملة"
            'allProductsSimple' => Product::orderBy('name')->get(), 
        ]);
    }
}