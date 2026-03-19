<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('paid_cash', 15, 2)->default(0)->after('amount');
            $table->decimal('paid_bankak', 15, 2)->default(0)->after('paid_cash');
            $table->string('payment_method')->default('cash')->after('paid_bankak');
            $table->string('transaction_number')->nullable()->after('payment_method');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['paid_cash', 'paid_bankak', 'payment_method', 'transaction_number']);
        });
    }
};