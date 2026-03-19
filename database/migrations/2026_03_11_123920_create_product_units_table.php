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
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->unique()->nullable(); // باركود خاص بالكرتونة يختلف عن باركود الحبة
            
            // إذا كان سعر الكرتونة كجملة أرخص من سعر القطع مفردة، نضع السعر هنا
            // إذا تُرك فارغاً (null)، سيقوم النظام بضرب (سعر القطعة الأساسي * معامل التحويل)
            $table->decimal('specific_selling_price', 15, 2)->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
