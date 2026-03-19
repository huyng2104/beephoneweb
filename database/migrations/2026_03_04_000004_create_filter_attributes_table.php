<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filter_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 120)->unique();
            $table->string('input_type', 30)->default('text');
            $table->text('suggested_values')->nullable();
            $table->boolean('is_filterable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filter_attributes');
    }
};
