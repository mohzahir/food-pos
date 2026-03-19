<?php

namespace App\Observers;

use App\Models\PurchaseItem;
use App\Models\Product;

class PurchaseItemObserver
{
    /**
     * Handle the PurchaseItem "created" event.
     */
    public function created(PurchaseItem $purchaseItem): void
    {
        $product = $purchaseItem->product;
        $unit = $purchaseItem->unit;

        // 1. حساب الكمية بالوحدة الأساسية لزيادتها في المخزن
        // (الكمية المشتراة × معامل تحويل الوحدة)
        $quantityToAdd = $purchaseItem->quantity * $unit->conversion_rate;

        // 2. تحديث الأسعار (كما فعلنا سابقاً)
        $baseCostPrice = $purchaseItem->unit_cost_price / $unit->conversion_rate;
        $baseSellingPrice = $purchaseItem->new_unit_selling_price / $unit->conversion_rate;

        // 3. تحديث السعر وزيادة المخزون في خطوة واحدة
        $product->current_cost_price = $baseCostPrice;
        $product->current_selling_price = $baseSellingPrice;
        $product->current_stock += $quantityToAdd; // إضافة الكمية الجديدة للرصيد الحالي
        $product->save();
    }
}