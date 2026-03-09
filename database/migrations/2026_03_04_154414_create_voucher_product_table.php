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
        Schema::create('voucher_product', function (Blueprint $table) {
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedBigInteger('product_id');

            $table->primary(['voucher_id', 'product_id']);

            $table->foreign('voucher_id')
                ->references('id')
                ->on('vouchers');

            $table->foreign('product_id')
                ->references('id')
                ->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_product');
    }
};
