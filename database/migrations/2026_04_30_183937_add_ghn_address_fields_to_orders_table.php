<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('ghn_district_id')->nullable()->after('shipping_address');
            $table->string('ghn_ward_code')->nullable()->after('ghn_district_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['ghn_district_id', 'ghn_ward_code']);
        });
    }
};
