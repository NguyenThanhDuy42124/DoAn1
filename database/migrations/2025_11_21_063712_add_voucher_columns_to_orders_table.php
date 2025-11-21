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
        Schema::table('orders', function (Blueprint $table) {
            // Lưu ID voucher đã dùng (nullable vì có đơn không dùng voucher)
            $table->foreignId('voucher_id')->nullable()->after('total_price')->constrained('vouchers');
            
            // Lưu số tiền được giảm thực tế
            $table->decimal('discount_amount', 15, 2)->default(0)->after('voucher_id');
            
            // Lưu tổng tiền hàng trước khi giảm (Subtotal)
            $table->decimal('subtotal', 15, 2)->default(0)->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
             $table->dropColumn(['voucher_id', 'discount_amount', 'subtotal']);
        });
    }
};
