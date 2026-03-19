<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\License;

class LicenseScreen extends Component
{
    public $machine_id;
    public $entered_key = '';

    public function mount()
    {
        $rawMachineId = php_uname('n') . php_uname('m'); 
        $machineId = strtoupper(substr(md5($rawMachineId), 0, 12));
        $this->machine_id = implode('-', str_split($machineId, 4));
    }

    public function activate()
    {
        // 1. تنظيف الإدخال: إزالة أي مسافات أو شرطات أدخلها المستخدم لتسهيل الأمر عليه
        $cleanedKey = strtoupper(str_replace(['-', ' '], '', $this->entered_key));

        // 2. التحقق من أن المفتاح غير فارغ وطوله 16 حرفاً/رقماً (بدون شرطات)
        if (empty($cleanedKey)) {
            $this->addError('entered_key', '⚠️ الرجاء إدخال مفتاح التفعيل أولاً.');
            return;
        }

        if (strlen($cleanedKey) !== 16) {
            $this->addError('entered_key', '⚠️ مفتاح التفعيل غير مكتمل. يجب أن يتكون من 16 حرفاً أو رقماً.');
            return;
        }

        // 3. الكلمة السرية وحساب المفتاح المتوقع
        $developerSecret = 'MY_SUPER_ERP_SUDAN_2026';
        $expectedKeyRaw = strtoupper(substr(md5($this->machine_id . $developerSecret), 0, 16));

        // 4. مطابقة المفتاح
        if ($cleanedKey === $expectedKeyRaw) {
            // إعادة تنسيق المفتاح للشكل الجميل قبل الحفظ
            $formattedKey = implode('-', str_split($cleanedKey, 4));
            
            License::updateOrCreate(
                ['id' => 1],
                [
                    'machine_id' => $this->machine_id,
                    'license_key' => $formattedKey,
                    'is_active' => true
                ]
            );

            // 🌟 الحل هنا: استخدام دالة التوجيه الخاصة بـ Livewire
            $this->redirect('/', navigate: true); 

        } else {
            // المفتاح خاطئ
            session()->flash('error', '❌ مفتاح التفعيل غير صحيح، هذا المفتاح لا يخص هذا الجهاز!');
        }
    }

    public function render()
    {
        return view('components.license-screen')->layout('layouts.guest'); 
    }
}