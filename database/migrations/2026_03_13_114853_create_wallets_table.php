<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 1: Quản lý số dư Ví
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết bảng users
            $table->decimal('balance', 15, 2)->default(0); // Số dư, tối đa 9 nghìn tỷ, 2 số thập phân
            $table->enum('status', ['active', 'locked'])->default('active'); // Trạng thái ví
            $table->timestamps();
        });

        // Bảng 2: Lịch sử giao dịch của Ví
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade'); // Liên kết bảng wallets
            $table->enum('type', ['deposit', 'payment', 'withdraw', 'refund']); // Loại giao dịch
            $table->decimal('amount', 15, 2); // Số tiền giao dịch
            $table->decimal('balance_before', 15, 2); // Số dư trước khi giao dịch
            $table->decimal('balance_after', 15, 2); // Số dư sau khi giao dịch
            $table->string('description')->nullable(); // Ghi chú (Ví dụ: Nạp tiền qua VNPay)
            $table->string('reference_id')->nullable(); // Mã tham chiếu (VD: ID đơn hàng)
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed'); // Trạng thái GD
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};