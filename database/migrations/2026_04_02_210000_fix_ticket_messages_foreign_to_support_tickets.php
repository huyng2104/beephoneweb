<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ticket_messages') || !Schema::hasTable('support_tickets')) {
            return;
        }

        Schema::table('ticket_messages', function (Blueprint $table) {
            try {
                $table->dropForeign(['ticket_id']);
            } catch (\Throwable $e) {
                // Ignore if old foreign key does not exist.
            }
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->foreign('ticket_id')
                ->references('id')
                ->on('support_tickets')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ticket_messages')) {
            return;
        }

        Schema::table('ticket_messages', function (Blueprint $table) {
            try {
                $table->dropForeign(['ticket_id']);
            } catch (\Throwable $e) {
                // Ignore in rollback if foreign key does not exist.
            }
        });
    }
};

