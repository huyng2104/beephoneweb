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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('status', ['new', 'processing', 'waiting', 'done'])->default('new');

            $table->unsignedBigInteger('admin_id')->nullable();
            $table->integer('customer_phone');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
