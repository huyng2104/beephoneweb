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
        Schema::table('wallets', function (Blueprint $table) {
            // Thêm các trường bảo mật & khóa lạc quan sau cột status
            $table->string('wallet_pin')->nullable()->after('status'); // Mã PIN ví đã mã hóa
            $table->tinyInteger('pin_attempts')->default(0)->after('wallet_pin'); // Số lần gõ sai PIN
            $table->dateTime('locked_until')->nullable()->after('pin_attempts'); // Mốc thời gian phạt khóa

            $table->string('lock_reason')->nullable()->after('locked_until');
            $table->bigInteger('version')->default(1)->after('locked_until'); // Phục vụ Optimistic Locking
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['wallet_pin', 'pin_attempts', 'locked_until', 'version', 'lock_reason']);
        });
    }
};
