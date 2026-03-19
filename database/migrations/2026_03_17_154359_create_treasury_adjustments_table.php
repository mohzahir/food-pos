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
        Schema::create('treasury_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // deposit_cash, withdrawal_cash, deposit_bankak, withdrawal_bankak, transfer_to_bank, transfer_to_cash
            $table->decimal('amount', 15, 2);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasury_adjustments');
    }
};
