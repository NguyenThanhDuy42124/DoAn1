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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            // Liên kết với bảng users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Số dư: Quan trọng nhất là dùng decimal để không bị sai số lẻ
            // 15 số tổng, 2 số thập phân (VND thì có thể để 0, nhưng Stripe hay trả về cents nên để 2 cho chắc)
            $table->decimal('balance', 15, 2)->default(0); 
            
            $table->string('currency', 3)->default('VND'); // VND hoặc USD
            $table->enum('status', ['active', 'locked'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
