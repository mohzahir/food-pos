<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\ProductUnit;

class SudaneseMarketSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء الفئات (بأيقوناتها لتظهر بشكل جميل في الكاشير)
        $categories = [
            'الأساسيات (البقالة الجافة) 🌾',
            'الزيوت والسمن 🛢️',
            'المعلبات 🥫',
            'الألبان والأجبان 🧀',
            'المشروبات والعصائر 🧃',
            'المنظفات 🧼',
            'عطارة وأوزان ⚖️' // فئة خاصة بالمنتجات الموزونة
        ];

        $categoryMap = [];
        foreach ($categories as $catName) {
            $categoryMap[$catName] = Category::firstOrCreate(['name' => $catName])->id;
        }

        // 2. إنشاء الوحدات الأساسية ووحدات الجملة
        $units = [
            'piece' => Unit::firstOrCreate(['name' => 'حبة'], ['type' => 'quantity', 'conversion_rate' => 1]),
            'kilo'  => Unit::firstOrCreate(['name' => 'كيلو'], ['type' => 'weight', 'conversion_rate' => 1]),
            
            // وحدات الجملة الشائعة في السودان
            'carton12' => Unit::firstOrCreate(['name' => 'كرتونة (12 حبة)'], ['type' => 'quantity', 'conversion_rate' => 12]),
            'carton24' => Unit::firstOrCreate(['name' => 'كرتونة (24 حبة)'], ['type' => 'quantity', 'conversion_rate' => 24]),
            'carton40' => Unit::firstOrCreate(['name' => 'كرتونة (40 حبة)'], ['type' => 'quantity', 'conversion_rate' => 40]),
            'packet10' => Unit::firstOrCreate(['name' => 'باكت (10 حبات)'], ['type' => 'quantity', 'conversion_rate' => 10]),
            'sack25'   => Unit::firstOrCreate(['name' => 'جوال (25 كيلو)'], ['type' => 'weight', 'conversion_rate' => 25]),
            'sack50'   => Unit::firstOrCreate(['name' => 'جوال (50 كيلو)'], ['type' => 'weight', 'conversion_rate' => 50]),
        ];

        // 3. قائمة المنتجات (السودانية والمصرية والموزونة)
        $productsData = [
            // --- منتجات سودانية (أساسيات) ---
            [
                'name' => 'سكر كنانة أبيض 1 كيلو', 'cat' => 'الأساسيات (البقالة الجافة) 🌾',
                'base_unit' => 'piece', 'cost' => 1800, 'sell' => 2000,
                'wholesale_unit' => 'sack50', 'wholesale_price' => 95000 // سعر الجوال
            ],
            [
                'name' => 'دقيق سيقا متعدد الاستخدامات 1 كيلو', 'cat' => 'الأساسيات (البقالة الجافة) 🌾',
                'base_unit' => 'piece', 'cost' => 1300, 'sell' => 1500,
                'wholesale_unit' => 'packet10', 'wholesale_price' => 14000 // سعر الباكت
            ],
            [
                'name' => 'زيت ياقوت عباد شمس 1 لتر', 'cat' => 'الزيوت والسمن 🛢️',
                'base_unit' => 'piece', 'cost' => 3800, 'sell' => 4200,
                'wholesale_unit' => 'carton12', 'wholesale_price' => 49000
            ],
            [
                'name' => 'صلصة سعيد مركزة 400 جم', 'cat' => 'المعلبات 🥫',
                'base_unit' => 'piece', 'cost' => 1200, 'sell' => 1400,
                'wholesale_unit' => 'carton12', 'wholesale_price' => 15500
            ],
            [
                'name' => 'شاي الغزالين أسود 225 جم', 'cat' => 'الأساسيات (البقالة الجافة) 🌾',
                'base_unit' => 'piece', 'cost' => 2500, 'sell' => 2800,
                'wholesale_unit' => 'carton24', 'wholesale_price' => null // يترك null ليحسب تلقائياً
            ],

            // --- واردات مصرية ---
            [
                'name' => 'عصير جهينة مانجو 1 لتر', 'cat' => 'المشروبات والعصائر 🧃',
                'base_unit' => 'piece', 'cost' => 1500, 'sell' => 1800,
                'wholesale_unit' => 'carton12', 'wholesale_price' => 20500
            ],
            [
                'name' => 'سمنة روابي أبيض 700 جم', 'cat' => 'الزيوت والسمن 🛢️',
                'base_unit' => 'piece', 'cost' => 4500, 'sell' => 5000,
                'wholesale_unit' => 'carton12', 'wholesale_price' => 58000
            ],
            [
                'name' => 'جبنة طعمة مثلثات 8 قطع', 'cat' => 'الألبان والأجبان 🧀',
                'base_unit' => 'piece', 'cost' => 900, 'sell' => 1200,
                'wholesale_unit' => 'carton40', 'wholesale_price' => 44000
            ],
            [
                'name' => 'مكرونة الملكة خواتم 400 جم', 'cat' => 'الأساسيات (البقالة الجافة) 🌾',
                'base_unit' => 'piece', 'cost' => 800, 'sell' => 1000,
                'wholesale_unit' => 'packet10', 'wholesale_price' => 9500
            ],

            // --- العطارة والأوزان المفتوحة (تباع بالكيلو أو جراماته) ---
            [
                'name' => 'ثوم صيني مقشر وزن (كيلو)', 'cat' => 'عطارة وأوزان ⚖️',
                'base_unit' => 'kilo', 'cost' => 5000, 'sell' => 7000, // يباع بالكيلو
                'wholesale_unit' => 'sack25', 'wholesale_price' => 160000 // سعر الشوال
            ],
            [
                'name' => 'قرفة عيدان وزن (كيلو)', 'cat' => 'عطارة وأوزان ⚖️',
                'base_unit' => 'kilo', 'cost' => 12000, 'sell' => 15000,
                'wholesale_unit' => null, 'wholesale_price' => null
            ],
            [
                'name' => 'عدس أحمر تركي وزن (كيلو)', 'cat' => 'عطارة وأوزان ⚖️',
                'base_unit' => 'kilo', 'cost' => 2000, 'sell' => 2500,
                'wholesale_unit' => 'sack25', 'wholesale_price' => 58000
            ],
        ];

        // 4. حلقة إدخال البيانات في قاعدة البيانات
        foreach ($productsData as $data) {
            // توليد باركود عشوائي مكون من 8 أرقام
            $sku = (string) rand(10000000, 99999999); 
            
            // مخزون افتراضي لبدء العمل (مثلاً 50 وحدة أساسية)
            $initialStock = 50; 

            $product = Product::create([
                'name' => $data['name'],
                'sku' => $sku,
                'category_id' => $categoryMap[$data['cat']],
                'base_unit_id' => $units[$data['base_unit']]->id,
                'current_cost_price' => $data['cost'],
                'current_selling_price' => $data['sell'],
                'current_stock' => $initialStock,
                'has_fraction' => true, // مهم جداً للأوزان
                'is_active' => true,
            ]);

            // ربط وحدة الجملة إذا كانت موجودة
            if ($data['wholesale_unit'] !== null) {
                ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_id' => $units[$data['wholesale_unit']]->id,
                    'barcode' => (string) rand(10000000, 99999999),
                    'specific_selling_price' => $data['wholesale_price'],
                ]);
            }
        }
    }
}