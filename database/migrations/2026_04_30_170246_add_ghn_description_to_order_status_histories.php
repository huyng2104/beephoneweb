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
        Schema::table('order_status_histories', function (Blueprint $table) {
            // Mô tả chi tiết từ GHN (VD: "Đơn hàng được giao thành công tại Xã Tiên Yên, Huyện Hoài Đức, Hà Nội")
            $table->string('ghn_description', 500)->nullable()->after('note');
            // Trạng thái gốc từ GHN (VD: "delivered", "delivering", ...)
            $table->string('ghn_status_raw')->nullable()->after('ghn_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropColumn(['ghn_description', 'ghn_status_raw']);
        });
    }
};
