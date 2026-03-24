<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_vouchers', function (Blueprint $table) {
            // Kiểm tra nếu chưa có cột order_id thì mới thêm (để tránh lỗi nếu bạn đã lỡ thêm tay)
            if (!Schema::hasColumn('user_vouchers', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('voucher_id');
            }

            // Thêm ràng buộc khóa ngoại
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('set null'); // Khi xóa đơn hàng, voucher vẫn giữ lại lịch sử nhưng order_id về null
        });
    }

    public function down(): void
    {
        Schema::table('user_vouchers', function (Blueprint $table) {
            // Xóa khóa ngoại trước khi xóa cột
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
