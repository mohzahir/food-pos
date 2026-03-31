<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ProductUnit;

class PriceUpdater extends Component
{
    use WithPagination;

    public $search = '';

    // تصفير الترقيم عند البحث
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // 1. دالة حفظ التكلفة لحظياً
    public function updateCostPrice($productId, $newPrice)
    {
        if (is_numeric($newPrice) && $newPrice >= 0) {
            Product::where('id', $productId)->update(['current_cost_price' => $newPrice]);
            $this->dispatch('price-saved'); // إرسال إشعار للواجهة بظهور علامة الصح
        }
    }

    // 2. دالة حفظ سعر القطاعي لحظياً
    public function updateRetailPrice($productId, $newPrice)
    {
        if (is_numeric($newPrice) && $newPrice >= 0) {
            Product::where('id', $productId)->update(['current_selling_price' => $newPrice]);
            $this->dispatch('price-saved');
        }
    }

    // 3. دالة حفظ تسعيرات الجملة لحظياً
    public function updateWholesalePrice($productUnitId, $newPrice)
    {
        if (is_numeric($newPrice) && $newPrice >= 0) {
            ProductUnit::where('id', $productUnitId)->update(['specific_selling_price' => $newPrice]);
            $this->dispatch('price-saved');
        } elseif ($newPrice === '') {
            // إذا ترك الحقل فارغاً، نحذف التسعيرة المخصصة
            ProductUnit::where('id', $productUnitId)->update(['specific_selling_price' => null]);
            $this->dispatch('price-saved');
        }
    }

    public function render()
    {
        // جلب المنتجات مع وحداتها (الأساسية والفرعية) مع البحث
        $productsQuery = Product::with(['baseUnit', 'productUnits.unit'])
            ->where('is_active', true);

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

        // عرض 20 منتج في كل صفحة لتخفيف الضغط وجعل الشاشة سريعة جداً
        $products = $productsQuery->orderBy('name')->paginate(20);

        return view('components.price-updater', [
            'products' => $products
        ]);
    }
}