<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Xóa các cột cũ/trùng lặp không còn sử dụng trong bảng orders.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['phone', 'recipient_name', 'recipient_phone', 'recipient_address', 'address', 'total_price'];

            foreach ($columns as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     * Khôi phục lại các cột nếu rollback.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('customer_name');
            $table->string('recipient_name')->nullable()->after('customer_email');
            $table->string('recipient_phone')->nullable()->after('recipient_name');
            $table->string('recipient_address')->nullable()->after('recipient_phone');
            $table->string('address')->nullable()->after('shipping_address');
            $table->decimal('total_price', 15, 2)->nullable()->after('address');
        });
    }
};
