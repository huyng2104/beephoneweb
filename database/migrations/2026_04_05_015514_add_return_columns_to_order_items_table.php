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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('return_status')->default('none');
            $table->text('return_note')->nullable();
            $table->string('return_image')->nullable();
            $table->text('return_admin_note')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->timestamp('return_approved_at')->nullable();
            $table->timestamp('return_rejected_at')->nullable();
            $table->timestamp('return_shipped_at')->nullable();
            $table->timestamp('return_received_at')->nullable();
            $table->timestamp('return_refunded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'return_status',
                'return_note',
                'return_image',
                'return_admin_note',
                'refund_amount',
                'return_requested_at',
                'return_approved_at',
                'return_rejected_at',
                'return_shipped_at',
                'return_received_at',
                'return_refunded_at',
            ]);
        });
    }
};
