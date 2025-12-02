<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Liên kết với ví nào
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            
            // Số tiền biến động (Dương là cộng vào, Âm là trừ đi)
            $table->decimal('amount', 15, 2);
            
            // Loại giao dịch
            // deposit: nạp tiền/nhận tiền đơn hàng
            // withdraw: rút tiền
            // commission: hoa hồng sàn nhận
            $table->enum('type', ['deposit', 'withdraw', 'payment', 'commission', 'refund']);
            
            // Mô tả giao dịch (Admin đọc cái này)
            $table->string('description')->nullable();
            
            // Quan trọng: Lưu ID của Stripe Payment Intent hoặc Order ID để đối soát
            $table->string('reference_id')->nullable()->index(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
