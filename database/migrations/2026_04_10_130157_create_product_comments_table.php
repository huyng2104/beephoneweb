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
        Schema::create('product_comments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            
            // Link to user if logged in, nullable if guest
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // For nested replies (parent comment)
            $table->foreignId('parent_id')->nullable()->constrained('product_comments')->cascadeOnDelete();
            
            // For guests
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            
            $table->text('content');
            
            // 0: pending, 1: approved, 2: hidden
            $table->tinyInteger('status')->default(1);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_comments');
    }
};
