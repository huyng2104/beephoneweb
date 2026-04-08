<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // === QUAN HỆ ===
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            // === NỘI DUNG ĐÁNH GIÁ ===
            $table->tinyInteger('rating')->unsigned()->comment('1–5 sao');
            $table->text('comment')->nullable()->comment('Nội dung nhận xét');

            // === TRẠNG THÁI ===
            // 0 = Chờ duyệt, 1 = Hiển thị, 2 = Ẩn/Từ chối
            $table->tinyInteger('status')->default(0)->comment('0: chờ duyệt, 1: hiển thị, 2: ẩn');

            // === TÍNH NĂNG NÂNG CAO ===
            // Đã mua hàng → tự động check qua đơn hàng
            $table->boolean('is_purchased')->default(false)->comment('Đã mua hàng tại BeePhone');

            // Hữu ích
            $table->unsignedInteger('helpful_count')->default(0)->comment('Số lượt bình chọn Hữu ích');

            // Phản hồi của Admin/nhân viên
            $table->text('reply_comment')->nullable()->comment('Phản hồi từ cửa hàng');
            $table->foreignId('replied_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin/nhân viên đã phản hồi');
            $table->timestamp('replied_at')->nullable();

            $table->timestamps();

            // === INDEX ===
            $table->index(['product_id', 'status']);
            $table->index(['user_id', 'product_id']);
        });

        // Bảng phụ lưu ảnh đính kèm (một review có nhiều ảnh)
        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')
                  ->constrained('reviews')
                  ->cascadeOnDelete();
            $table->string('image_path')->comment('Đường dẫn ảnh trên storage');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_images');
        Schema::dropIfExists('reviews');
    }
};
