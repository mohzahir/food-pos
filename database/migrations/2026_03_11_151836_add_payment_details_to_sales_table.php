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
        Schema::table('sales', function (Blueprint $table) {
            // ربط الفاتورة بعميل معين (nullable لأن زبون القطاعي العابر لا يحتاج لتسجيل اسمه)
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete()->after('id');
            
            // تفاصيل المبالغ والديون
            $table->decimal('paid_amount', 15, 2)->default(0)->after('total_amount'); // المبلغ المدفوع
            $table->decimal('remaining_amount', 15, 2)->default(0)->after('paid_amount'); // المتبقي (الدين)
            
            // طريقة الدفع وحالة الفاتورة
            $table->string('payment_method')->default('cash')->after('remaining_amount'); // cash, bankak
            $table->string('transaction_number')->nullable()->after('payment_method'); // رقم إشعار بنكك إن وجد
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('paid')->after('transaction_number'); // حالة الدفع
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'paid_amount', 'remaining_amount', 'payment_method', 'transaction_number', 'payment_status']);
        });
    }
};
