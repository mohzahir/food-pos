<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('paid_cash', 15, 2)->default(0)->after('paid_amount');
            $table->decimal('paid_bankak', 15, 2)->default(0)->after('paid_cash');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('paid_cash', 15, 2)->default(0)->after('paid_amount');
            $table->decimal('paid_bankak', 15, 2)->default(0)->after('paid_cash');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['paid_cash', 'paid_bankak']);
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['paid_cash', 'paid_bankak']);
        });
    }
};