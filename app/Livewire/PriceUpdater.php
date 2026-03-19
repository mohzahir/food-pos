<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;

class PriceUpdater extends Component
{
    public $selected_product = '';
    
    // وحدة التسعير الأساسية (التي سيدخل التاجر بناءً عليها التكلفة وسعر القطاعي)
    public $pricing_unit_id = '';
    public $pricing_unit_name = '';
    public $conversion_rate = 1;
    
    // حقول أسعار التكلفة والقطاعي
    public $cost_price = '';
    public $retail_price = ''; 
    
    // مصفوفة ديناميكية لتخزين أسعار كل وحدات الجملة [id => price]
    public $wholesale_prices = [];
    
    // بيانات للعرض في الواجهة
    public $available_units = [];
    public $wholesale_records = [];

    public function updatedSelectedProduct($productId)
    {
        // تصفير البيانات القديمة عند تغيير المنتج
        $this->reset(['pricing_unit_id', 'cost_price', 'retail_price', 'wholesale_prices', 'available_units', 'wholesale_records', 'pricing_unit_name', 'conversion_rate']);
        
        if ($productId) {
            $product = Product::with('baseUnit')->find($productId);
            
            // 1. جلب كل تسعيرات الجملة (الباركودات الفرعية) لهذا المنتج
            $this->wholesale_records = ProductUnit::with('unit')->where('product_id', $productId)->get();
            
            $units = collect();
            if ($product->baseUnit) {
                $units->push($product->baseUnit);
            }
            
            // 2. تعبئة مصفوفة أسعار الجملة للواجهة وإضافة الوحدات للقائمة
            foreach ($this->wholesale_records as $record) {
                $units->push($record->unit);
                $this->wholesale_prices[$record->id] = $record->specific_selling_price;
            }
            
            // تصفية الوحدات من التكرار وترتيبها من الأكبر للأصغر (لنجعل الوحدة الكبرى هي الافتراضية)
            $this->available_units = $units->unique('id')->sortByDesc('conversion_rate')->values()->all();
            
            // 3. اختيار الوحدة الكبرى كخيار افتراضي لحساب التكلفة والقطاعي
            if (count($this->available_units) > 0) {
                $this->pricing_unit_id = $this->available_units[0]['id'];
                $this->updatePricingBaseline();
            }
        }
    }

    // عندما يغير التاجر وحدة قياس التكلفة (مثلاً من 50 كيلو إلى 5 كيلو)
    public function updatedPricingUnitId()
    {
        $this->updatePricingBaseline();
    }

    private function updatePricingBaseline()
    {
        if ($this->selected_product && $this->pricing_unit_id) {
            $product = Product::find($this->selected_product);
            $unit = Unit::find($this->pricing_unit_id);
            
            if ($product && $unit) {
                $this->pricing_unit_name = $unit->name;
                $this->conversion_rate = $unit->conversion_rate;
                
                // حساب وعرض التكلفة والسعر الحالي بناءً على الوحدة المختارة
                $this->cost_price = $product->current_cost_price * $this->conversion_rate;
                $this->retail_price = $product->current_selling_price * $this->conversion_rate;
            }
        }
    }

    public function updatePrice()
    {
        $this->validate([
            'selected_product' => 'required',
            'pricing_unit_id' => 'required',
            'cost_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            // التحقق من أن كل أسعار الجملة المدخلة صحيحة (أرقام)
            'wholesale_prices.*' => 'nullable|numeric|min:0',
        ]);

        $product = Product::find($this->selected_product);

        // 1. تحديث التكلفة وسعر القطاعي (للوحدة الأساسية)
        $baseCost = $this->cost_price / $this->conversion_rate;
        $baseRetail = $this->retail_price / $this->conversion_rate;

        $product->update([
            'current_cost_price' => $baseCost,
            'current_selling_price' => $baseRetail,
        ]);

        // 2. تحديث كل تسعيرات الجملة بضغطة واحدة!
        foreach ($this->wholesale_prices as $recordId => $price) {
            ProductUnit::where('id', $recordId)->update([
                'specific_selling_price' => $price !== '' ? $price : null
            ]);
        }

        session()->flash('success', 'تم تحديث جميع أسعار المنتج (القطاعي وكل وحدات الجملة) بنجاح!');
        $this->reset(['selected_product', 'pricing_unit_id', 'cost_price', 'retail_price', 'wholesale_prices', 'available_units', 'wholesale_records', 'pricing_unit_name', 'conversion_rate']);
    }

    public function render()
    {
        return view('components.price-updater', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}