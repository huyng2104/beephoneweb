<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('comments');
    }

    public function down(): void
    {
        // Tạo lại bảng comments nếu rollback
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->integer('rating')->nullable();
            $table->text('content');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('verified_purchase')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });
    }
};
