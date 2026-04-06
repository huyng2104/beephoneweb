<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Xóa các cột return ở cấp Order (đã chuyển xuống cấp OrderItem).
     * Giữ lại: return_status (vẫn dùng để lọc/hiển thị tổng quan đơn hàng)
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $toDrop = [
                'return_note',
                'return_image',
                'return_admin_note',
                'return_requested_at',
                'return_approved_at',
                'return_rejected_at',
                'return_shipped_at',
                'return_received_at',
                'return_refunded_at',
                'refund_amount',
            ];

            foreach ($toDrop as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('return_note')->nullable();
            $table->string('return_image')->nullable();
            $table->text('return_admin_note')->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->timestamp('return_approved_at')->nullable();
            $table->timestamp('return_rejected_at')->nullable();
            $table->timestamp('return_shipped_at')->nullable();
            $table->timestamp('return_received_at')->nullable();
            $table->timestamp('return_refunded_at')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
        });
    }
};
