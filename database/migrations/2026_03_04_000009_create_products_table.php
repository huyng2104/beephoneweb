<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable(); // Đã thêm slug
            $table->text('description')->nullable(); // Mô tả
            $table->enum('type', ['simple', 'variable'])->default('simple'); // Loại SP
            $table->decimal('price', 15, 2)->default(0); // Giá thường
            $table->decimal('sale_price', 15, 2)->nullable(); // Giá KM
            $table->string('sku')->nullable(); // Mã SP
            $table->integer('stock')->default(0); // Tồn kho
            $table->enum('status', ['active', 'inactive'])->default('active'); // Trạng thái
            $table->boolean('is_featured')->default(0); // Nổi bật
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete(); // Khóa ngoại Thương hiệu
            $table->string('thumbnail')->nullable(); // Ảnh đại diện
            
            $table->timestamps();
            $table->softDeletes(); // Xóa mềm
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
