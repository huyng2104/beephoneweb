<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chatbot_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question')->unique();
            $table->text('answer');
            $table->string('keywords')->nullable(); // comma-separated keywords
            $table->integer('priority')->default(0); // higher = show first
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chatbot_faqs');
    }
};
