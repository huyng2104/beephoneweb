<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_request_histories', function (Blueprint $table) {
            $table->string('ghn_status_raw', 50)->nullable()->after('status');
            $table->text('ghn_description')->nullable()->after('ghn_status_raw');
        });
    }

    public function down(): void
    {
        Schema::table('return_request_histories', function (Blueprint $table) {
            $table->dropColumn(['ghn_status_raw', 'ghn_description']);
        });
    }
};
