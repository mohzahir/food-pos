<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class InventoryScreen extends Component
{
    public $search = '';

    public function render()
    {
        // جلب المنتجات مع وحداتها الأساسية ووحدات الجملة المربوطة بها
        $products = Product::with(['baseUnit', 'productUnits.unit'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('sku', 'like', '%' . $this->search . '%')
            ->orderBy('current_stock', 'asc')
            ->get();

        $totalInventoryCost = 0; 
        $totalExpectedRevenue = 0; 

        foreach ($products as $p) {
            $totalInventoryCost += ($p->current_stock * $p->current_cost_price);
            $totalExpectedRevenue += ($p->current_stock * $p->current_selling_price);
        }

        $expectedProfit = $totalExpectedRevenue - $totalInventoryCost;

        return view('components.inventory-screen', [
            'products' => $products,
            'totalInventoryCost' => $totalInventoryCost,
            'totalExpectedRevenue' => $totalExpectedRevenue,
            'expectedProfit' => $expectedProfit,
        ]);
    }
}