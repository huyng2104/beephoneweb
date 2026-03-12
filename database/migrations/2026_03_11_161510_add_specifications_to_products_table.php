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
    Schema::table('products', function (Blueprint $table) {
        // Thêm cột specifications kiểu JSON, cho phép rỗng, đặt sau cột description
        $table->json('specifications')->nullable()->after('description');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('specifications');
    });
}
};
