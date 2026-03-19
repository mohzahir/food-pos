<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description'); // تفاصيل المنصرف (مثلاً: أكياس بلاستيك)
            $table->string('category')->default('نثريات'); // تصنيف المنصرف
            $table->decimal('amount', 15, 2); // المبلغ
            $table->date('expense_date'); // تاريخ المنصرف
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};