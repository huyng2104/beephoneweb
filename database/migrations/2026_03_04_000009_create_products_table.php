<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique()->nullable();
                $table->text('description')->nullable();
                $table->enum('type', ['simple', 'variable'])->default('simple');
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('sale_price', 15, 2)->nullable();
                $table->string('sku')->nullable();
                $table->integer('stock')->default(0);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->boolean('is_featured')->default(0);
                $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
                $table->string('thumbnail')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }

            if (!Schema::hasColumn('products', 'type')) {
                $table->enum('type', ['simple', 'variable'])->default('simple')->after('description');
            }

            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('type');
            }

            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 15, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('sale_price');
            }

            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('sku');
            }

            if (!Schema::hasColumn('products', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('stock');
            }

            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(0)->after('status');
            }

            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->unsignedBigInteger('brand_id')->nullable()->after('is_featured');
            }

            if (!Schema::hasColumn('products', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('brand_id');
            }

            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
