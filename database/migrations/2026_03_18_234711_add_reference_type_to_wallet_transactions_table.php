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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Thêm cột reference_type (cho phép null) và đặt nó ngay sau cột reference_id cho dễ nhìn trong DB
            $table->string('reference_type')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Xóa cột nếu bạn chạy lệnh rollback
            $table->dropColumn('reference_type');
        });
    }
};
