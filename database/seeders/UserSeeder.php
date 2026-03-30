<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Unit;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Category;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 0. إنشاء المستخدمين (المدير والكاشير)
        // ==========================================
        
        // إنشاء المدير (Admin)
        User::firstOrCreate(
            ['email' => 'admin@pos.com'], // البحث عن هذا الإيميل
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('123456'), // كلمة المرور الافتراضية
                'role' => 'admin', // صلاحية المدير
            ]
        );

        // إنشاء الكاشير (Cashier)
        User::firstOrCreate(
            ['email' => 'cashier@pos.com'], // البحث عن هذا الإيميل
            [
                'name' => 'نقطة بيع 1',
                'password' => Hash::make('123456'), // كلمة المرور الافتراضية
                'role' => 'cashier', // صلاحية الكاشير
            ]
        );
    }
}