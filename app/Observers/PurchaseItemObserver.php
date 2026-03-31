<?php

namespace App\Observers;

use App\Models\PurchaseItem;
use App\Models\Product;

class PurchaseItemObserver
{
    public function created(PurchaseItem $purchaseItem): void
    {
        $product = $purchaseItem->product;
        $unit = $purchaseItem->unit;

        $quantityToAdd = $purchaseItem->quantity * $unit->conversion_rate;
        $baseCostPrice = $purchaseItem->unit_cost_price / $unit->conversion_rate;
        $baseSellingPrice = $purchaseItem->new_unit_selling_price / $unit->conversion_rate;

        $product->current_cost_price = $baseCostPrice;
        $product->current_selling_price = $baseSellingPrice;
        $product->current_stock += $quantityToAdd;
        $product->save();
    }

    // 🌟 الدالة الجديدة: عكس المخزون عند الحذف أو الإلغاء
    public function deleted(PurchaseItem $purchaseItem): void
    {
        $product = $purchaseItem->product;
        $unit = $purchaseItem->unit;

        // حساب الكمية بالوحدة الأساسية لخصمها (إرجاعها)
        $quantityToSubtract = $purchaseItem->quantity * $unit->conversion_rate;

        // خصم الكمية من المخزون
        $product->current_stock -= $quantityToSubtract;
        $product->save();
        
        // ملاحظة: لا نغير تسعيرة المنتج هنا، لأن آخر تسعيرة في السوق تظل كما هي حتى لو ألغينا الفاتورة.
    }
}