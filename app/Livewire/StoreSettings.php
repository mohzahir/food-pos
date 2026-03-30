<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Response;

class StoreSettings extends Component
{
    public $store_name, $phone, $address, $receipt_footer;

    public function mount()
    {
        $settings = Setting::first();
        if($settings) {
            $this->store_name = $settings->store_name;
            $this->phone = $settings->phone;
            $this->address = $settings->address;
            $this->receipt_footer = $settings->receipt_footer;
        }
    }

    public function save()
    {
        Setting::first()->update([
            'store_name' => $this->store_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'receipt_footer' => $this->receipt_footer,
        ]);

        session()->flash('message', '✅ تم تحديث إعدادات المتجر بنجاح');
    }

    // 🌟 الدالة السحرية للنسخ الاحتياطي 🌟
    public function downloadBackup()
    {
        // تحديد مسار ملف قاعدة البيانات
        $dbPath = database_path('database.sqlite');
        
        // تسمية الملف بتاريخ اليوم والوقت (مثال: Yaseer-Backup-2026-03-19.sqlite)
        $fileName = 'Yaseer-Backup-' . date('Y-m-d_H-i') . '.sqlite';
        
        // التأكد من وجود الملف ثم تحميله
        if (file_exists($dbPath)) {
            return Response::download($dbPath, $fileName);
        }
        
        session()->flash('error', 'ملف قاعدة البيانات غير موجود!');
    }

    public function render()
    {
        return view('components.store-settings')->layout('layouts.app');
    }
}