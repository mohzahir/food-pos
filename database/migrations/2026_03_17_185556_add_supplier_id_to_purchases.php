<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            // نضيف المعرف ونربطه بجدول الموردين
            $table->foreignId('supplier_id')->nullable()->after('supplier_name')->constrained('suppliers')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
