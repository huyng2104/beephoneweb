<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Thêm cột phone và role vào sau cột password cho gọn gàng
            $table->string('phone')->nullable()->after('password');
            $table->string('role')->default('user')->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nếu có rollback thì nó sẽ xóa 2 cột này đi
            $table->dropColumn(['phone', 'role']);
        });
    }
};