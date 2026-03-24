<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('point_settings', function (Blueprint $table) {
        $table->id();
        $table->integer('earn_rate')->default(100000)->comment('Số tiền chi tiêu để được 1 điểm');
        $table->integer('redeem_rate')->default(1000)->comment('Giá trị 1 điểm quy đổi ra VNĐ');
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_settings');
    }
};
