<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('verified_purchase')->default(false)->after('image_path');
            $table->boolean('is_hidden')->default(false)->after('verified_purchase');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['verified_purchase', 'is_hidden']);
        });
    }
};

