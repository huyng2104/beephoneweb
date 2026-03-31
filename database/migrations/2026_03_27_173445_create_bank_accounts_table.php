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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết tới bảng Users

            $table->string('bank_name'); // Tên ngân hàng (Ví dụ: Vietcombank, Techcombank)
            $table->string('bank_code'); // Mã ngân hàng chuẩn Napas (Ví dụ: VCB, TCB, MB) - Phục vụ API tự động
            $table->string('account_number'); // Số tài khoản ngân hàng
            $table->string('account_name'); // Tên chủ tài khoản (Viết hoa không dấu)
            $table->boolean('is_default')->default(false); // Đánh dấu đây là tài khoản rút tiền mặc định
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
