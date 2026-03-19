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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('unit_id')->constrained(); // الوحدة التي اشترينا بها (مثلاً: كرتونة، شوال)
            
            $table->decimal('quantity', 10, 3); // الكمية المشتراة
            $table->decimal('unit_cost_price', 15, 2); // تكلفة الشراء لهذه الوحدة
            $table->decimal('new_unit_selling_price', 15, 2); // سعر البيع الجديد الذي قرره التاجر لهذه الوحدة
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
