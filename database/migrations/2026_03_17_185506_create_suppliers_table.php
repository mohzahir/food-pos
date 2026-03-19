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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المورد أو المندوب
            $table->string('company')->nullable(); // اسم الشركة (سيقا، دال، الخ)
            $table->string('phone')->nullable();
            $table->decimal('balance', 15, 2)->default(0); // الديون التي علينا للمورد
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
