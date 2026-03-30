<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InventoryTransaction;
use App\Models\Product;

class InventoryMovementScreen extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = ''; // فلتر لنوع الحركة (تسوية، مبيعات، الخ)
    public $date_from = '';
    public $date_to = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingTypeFilter() { $this->resetPage(); }

    public function render()
    {
        $query = InventoryTransaction::with(['product', 'user'])->latest();

        // 1. البحث باسم المنتج أو الباركود
        if (!empty($this->search)) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        // 2. فلترة بنوع الحركة
        if (!empty($this->type_filter)) {
            $query->where('type', $this->type_filter);
        }

        // 3. فلترة بالتاريخ
        if (!empty($this->date_from)) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }
        if (!empty($this->date_to)) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        return view('components.inventory-movement-screen', [
            'transactions' => $query->paginate(20)
        ])->layout('layouts.app');
    }
}