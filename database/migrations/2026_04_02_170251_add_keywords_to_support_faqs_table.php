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
        Schema::table('support_faqs', function (Blueprint $table) {
            if (!Schema::hasColumn('support_faqs', 'keywords')) {
                $table->string('keywords')->nullable()->after('answer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_faqs', function (Blueprint $table) {
            if (Schema::hasColumn('support_faqs', 'keywords')) {
                $table->dropColumn('keywords');
            }
        });
    }
};
