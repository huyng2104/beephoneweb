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
        // Thực thi xóa bảng cũ
        Schema::dropIfExists('activity_logs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // (Tùy chọn) Tạo lại bảng nếu lỡ rollback migration này
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
