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
            $table->decimal('paid_amount', 15, 2)->default(0)->after('total_amount');
            $table->decimal('remaining_amount', 15, 2)->default(0)->after('paid_amount');
            $table->string('payment_status')->default('paid')->after('remaining_amount'); // paid, partial, unpaid
            $table->string('payment_method')->nullable()->after('payment_status'); // cash, bankak
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            //
        });
    }
};
