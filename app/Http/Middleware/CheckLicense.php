<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\License;

class CheckLicense
{
    public function handle(Request $request, Closure $next)
    {
        // 1. توليد بصمة الجهاز
        $rawMachineId = php_uname('n') . php_uname('m'); 
        $machineId = strtoupper(substr(md5($rawMachineId), 0, 12));
        $machineIdFormatted = implode('-', str_split($machineId, 4)); 

        // 2. الكلمة السرية
        $developerSecret = 'MY_SUPER_ERP_SUDAN_2026';

        // 3. حساب المفتاح الصحيح
        $expectedKeyRaw = strtoupper(substr(md5($machineIdFormatted . $developerSecret), 0, 16));
        $expectedKeyFormatted = implode('-', str_split($expectedKeyRaw, 4));

        // 4. فحص قاعدة البيانات
        $license = License::first();

        // 5. التحقق واتخاذ القرار
        if (!$license || $license->license_key !== $expectedKeyFormatted || !$license->is_active) {
            // توجيه لصفحة التفعيل
            return redirect()->route('license.activate');
        }

        return $next($request);
    }
}