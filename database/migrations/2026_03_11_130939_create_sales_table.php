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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique(); // رقم الفاتورة
            $table->decimal('total_amount', 15, 2)->default(0); // إجمالي الفاتورة
            $table->decimal('discount', 15, 2)->default(0); // الخصم إن وجد
            $table->enum('type', ['retail', 'wholesale'])->default('retail'); // قطاعي أو جملة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
