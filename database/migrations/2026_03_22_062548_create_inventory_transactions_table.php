<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // لمعرفة من قام بالتعديل
            
            $table->string('type'); // نوع الحركة: تسوية، مبيعات، مشتريات، مرتجع
            $table->decimal('quantity', 10, 2); // كمية التغيير (+ أو -)
            $table->decimal('balance_before', 10, 2); // الرصيد قبل التعديل
            $table->decimal('balance_after', 10, 2); // الرصيد بعد التعديل
            $table->string('notes')->nullable(); // سبب التعديل (مثل: عجز مجهول)
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_transactions');
    }
};