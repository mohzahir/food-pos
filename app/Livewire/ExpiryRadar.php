<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PurchaseItem;
use App\Models\Product; // الموديل الجديد المطلوب
use Carbon\Carbon;

class ExpiryRadar extends Component
{
    public function render()
    {
        $today = Carbon::today();
        $allItems = collect(); // سلة نجمع فيها البيانات من المصدرين

        // 1. جلب التواريخ من "المخزون الافتتاحي" (جدول المنتجات)
        $initialStockExpiring = Product::with('baseUnit')
            ->whereNotNull('expiry_date')
            ->where('current_stock', '>', 0) // فقط التي بها مخزون
            ->get()
            ->map(function ($product) use ($today) {
                $expiryDate = Carbon::parse($product->expiry_date);
                return (object)[
                    'source' => 'مخزون افتتاحي',
                    'product_name' => $product->name,
                    'unit_name' => $product->baseUnit->name ?? 'الوحدة الأساسية',
                    'supplier_name' => 'رصيد أول المدة',
                    'quantity' => $product->current_stock, // يعرض الكمية الإجمالية الحالية
                    'purchase_date' => $product->created_at->format('Y-m-d'),
                    'expiry_date' => $product->expiry_date,
                    'days_left' => $today->diffInDays($expiryDate, false)
                ];
            });

        // 2. جلب التواريخ من "المشتريات" (جدول المشتريات)
        $purchasedExpiring = PurchaseItem::with(['product', 'unit', 'purchase'])
            ->whereNotNull('expiry_date')
            ->get()
            ->map(function ($item) use ($today) {
                $expiryDate = Carbon::parse($item->expiry_date);
                return (object)[
                    'source' => 'فاتورة مشتريات',
                    'product_name' => $item->product->name ?? 'منتج محذوف',
                    'unit_name' => $item->unit->name ?? '-',
                    'supplier_name' => $item->purchase->supplier ? $item->purchase->supplier->name : ($item->purchase->supplier_name ?: 'مورد غير محدد'),
                    'quantity' => (float) $item->quantity,
                    'purchase_date' => date('Y-m-d', strtotime($item->purchase->purchase_date)),
                    'expiry_date' => $item->expiry_date,
                    'days_left' => $today->diffInDays($expiryDate, false)
                ];
            });

        // 3. دمج المجموعتين، وترتيبهما حسب الأقرب انتهاءً
        $expiringItems = $allItems->concat($initialStockExpiring)
                                  ->concat($purchasedExpiring)
                                  ->sortBy('days_left')
                                  ->values();

        return view('components.expiry-radar', [
            'expiringItems' => $expiringItems
        ])->layout('layouts.app'); 
    }
}