<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // إضافة حقل تكلفة الوحدة وقت الحركة
            $table->decimal('unit_cost', 15, 2)->nullable()->after('balance_after');
            
            // إضافة حقل القيمة الإجمالية للحركة (التكلفة × الكمية)
            $table->decimal('total_value', 15, 2)->nullable()->after('unit_cost');
        });
    }

    public function down()
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['unit_cost', 'total_value']);
        });
    }
};