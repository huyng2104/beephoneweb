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
        Schema::table('vouchers', function (Blueprint $table) {
            // Đã xóa ->after('quantity')
            $table->integer('points_required')->default(0)->comment('Số điểm để đổi Voucher');
        });
    }
public function down()
{
    Schema::table('vouchers', function (Blueprint $table) {
        $table->dropColumn('points_required');
    });
}

    /**
     * Reverse the migrations.
     */
   
};
