<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'return_admin_note')) {
                $table->text('return_admin_note')->nullable()->after('return_note');
            }

            if (! Schema::hasColumn('orders', 'return_approved_at')) {
                $table->timestamp('return_approved_at')->nullable()->after('return_requested_at');
            }

            if (! Schema::hasColumn('orders', 'return_rejected_at')) {
                $table->timestamp('return_rejected_at')->nullable()->after('return_approved_at');
            }

            if (! Schema::hasColumn('orders', 'return_shipped_at')) {
                $table->timestamp('return_shipped_at')->nullable()->after('return_rejected_at');
            }

            if (! Schema::hasColumn('orders', 'return_received_at')) {
                $table->timestamp('return_received_at')->nullable()->after('return_shipped_at');
            }

            if (! Schema::hasColumn('orders', 'return_refunded_at')) {
                $table->timestamp('return_refunded_at')->nullable()->after('return_received_at');
            }

            if (! Schema::hasColumn('orders', 'refund_amount')) {
                $table->integer('refund_amount')->nullable()->after('return_refunded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'return_admin_note',
                'return_approved_at',
                'return_rejected_at',
                'return_shipped_at',
                'return_received_at',
                'return_refunded_at',
                'refund_amount',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
