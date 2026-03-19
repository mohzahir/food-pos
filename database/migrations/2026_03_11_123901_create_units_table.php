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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الوحدة: مثلاً "كيلو"، "رطل"، "كرتونة"
            $table->enum('type', ['weight', 'quantity']); // نوع الوحدة لمعرفة هل تقبل كسور أم لا
            $table->decimal('conversion_rate', 10, 3)->default(1); // معامل التحويل مقارنة بالوحدة الأساسية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
