<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột tracking_number để lưu mã vận đơn GHN.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mã vận đơn GHN (VD: GHN12345678), nullable vì không phải đơn nào cũng có
            $table->string('tracking_number')->nullable()->after('note')->comment('Mã vận đơn GHN');
        });
    }

    /**
     * Rollback: xóa cột tracking_number.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('tracking_number');
        });
    }
};

