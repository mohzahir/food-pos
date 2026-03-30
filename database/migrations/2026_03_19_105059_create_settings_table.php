<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('يسير للخدمات');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('receipt_header')->nullable(); // رسالة أعلى الفاتورة
            $table->text('receipt_footer')->nullable(); // رسالة أسفل الفاتورة
            $table->string('logo')->nullable(); // مسار الشعار
            $table->string('currency')->default('SDG'); // العملة
            $table->timestamps();
        });

        // إضافة بيانات افتراضية فوراً عند إنشاء الجدول
        \App\Models\Setting::create([
            'store_name' => 'محلات الدول التجارية',
            'phone' => '0123456789',
            'address' => 'الولاية الشمالية - السوق الرئيسي',
            'receipt_footer' => 'شكراً لزيارتكم!'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
