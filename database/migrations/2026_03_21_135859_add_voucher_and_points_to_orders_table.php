<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('voucher_id')->nullable()->after('total_amount');
            $table->integer('discount_amount')->default(0)->after('voucher_id')->comment('Số tiền được giảm từ Voucher');
            $table->integer('points_earned')->default(0)->after('discount_amount')->comment('Số điểm tích lũy được từ đơn này');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['voucher_id', 'discount_amount', 'points_earned']);
        });
    }
};