<?php

namespace App\Observers;

use App\Models\SaleItem;

class SaleItemObserver
{
    public function created(SaleItem $saleItem): void
    {
        $product = $saleItem->product;
        $unit = $saleItem->unit;

        // حساب الكمية بالوحدة الأساسية لخصمها من المخزن
        // مثلاً: الزبون اشترى 1.5 كيلو (1.5 × 1000 = 1500 جرام سيتم خصمها)
        $quantityToSubtract = $saleItem->quantity * $unit->conversion_rate;

        // خصم الكمية من المخزون
        $product->decrement('current_stock', $quantityToSubtract);
    }
    
    // ملاحظة هامة: يجب أيضاً معالجة الإرجاع (عند حذف صنف من الفاتورة)
    public function deleted(SaleItem $saleItem): void
    {
        $product = $saleItem->product;
        $unit = $saleItem->unit;

        // إذا تم إرجاع الصنف، نعيد الكمية للمخزن
        $quantityToReturn = $saleItem->quantity * $unit->conversion_rate;
        $product->increment('current_stock', $quantityToReturn);
    }
}