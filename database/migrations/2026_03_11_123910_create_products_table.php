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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable(); // رمز المنتج الفريد
            $table->foreignId('base_unit_id')->constrained('units'); // الوحدة الأساسية (أصغر وحدة: جرام أو قطعة)
            
            // الأسعار الحالية (هذه التي ستتغير تلقائياً مع كل فاتورة مشتريات جديدة)
            $table->decimal('current_cost_price', 15, 2)->default(0); // التكلفة الحالية
            $table->decimal('current_selling_price', 15, 2)->default(0); // سعر البيع الحالي
            
            $table->boolean('has_fraction')->default(false); // هل يباع بالكسور؟ (مهم جداً للأوزان مثل 1.5 كيلو)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
