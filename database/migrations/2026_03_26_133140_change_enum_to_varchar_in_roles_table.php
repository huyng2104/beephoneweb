<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Thay chữ 'name' thành tên cột thực tế trong bảng roles của bạn (ví dụ: 'role_name' hoặc 'role')
            $table->string('name')->change();
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Rollback lại kiểu ENUM nếu cần (nhớ thay đổi danh sách mảng cho khớp với code cũ của bạn)
            $table->enum('name', ['admin', 'staff'])->change();
        });
    }
};
