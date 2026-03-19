<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            // إضافة حقل تاريخ الانتهاء كحقل اختياري (nullable)
            $table->date('expiry_date')->nullable()->after('new_unit_selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn('expiry_date');
        });
    }
};