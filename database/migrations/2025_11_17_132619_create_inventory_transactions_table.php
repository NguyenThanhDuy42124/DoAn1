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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            
            // Giả định bảng 'products' đã tồn tại
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade'); 
            
            // Giả định Seller là 'users' (bảng user mặc định của Laravel)
            $table->foreignId('seller_id')
                  ->constrained('users') // <-- Sửa 'users' nếu bảng seller của bạn tên khác
                  ->onDelete('cascade');

            $table->enum('transaction_type', ['import', 'export', 'sale']);
            $table->unsignedInteger('quantity'); // Luôn là số dương
            $table->text('notes')->nullable();
            
            // timestamps() sẽ tự động tạo created_at và updated_at
            // Ghi log thì chỉ cần created_at là đủ, nhưng timestamps() là chuẩn Laravel
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};