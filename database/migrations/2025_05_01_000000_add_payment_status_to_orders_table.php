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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->default('cod')->after('status'); // cod: thanh toán khi nhận hàng, vnpay: thanh toán qua vnpay
            $table->string('payment_status')->default('Chưa thanh toán')->after('payment_method'); // Chưa thanh toán, Thanh toán thành công, Thanh toán thất bại
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status']);
        });
    }
};
