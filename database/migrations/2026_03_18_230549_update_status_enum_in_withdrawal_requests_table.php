<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'canceled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khi rollback, nhớ phải xóa những record có status là 'canceled' trước (nếu có) để tránh lỗi data
        DB::table('withdrawal_requests')->where('status', 'canceled')->delete();

        // Sau đó mới đưa cột status về lại trạng thái cũ (chỉ có 3 giá trị)
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
