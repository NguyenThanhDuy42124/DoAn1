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
        Schema::table('vouchers', function (Blueprint $table) {
            // 1. Thêm cột mới
            $table->string('code')->unique()->after('seller_id'); // Mã nhập (SALE100)
            $table->string('name')->after('code'); // Tên voucher

            $table->enum('type', ['fixed', 'percent'])->default('fixed')->after('name');
            
            // Sửa thành (15, 2) để lỡ giảm giá 12.5% thì vẫn lưu được số lẻ
            $table->decimal('value', 15, 2)->after('type'); 
            
            $table->decimal('max_discount_amount', 15, 2)->nullable()->after('value');
            $table->decimal('min_order_value', 15, 2)->default(0)->after('max_discount_amount');
            
            $table->integer('quantity')->default(0)->after('min_order_value');
            $table->integer('used_count')->default(0)->after('quantity');
            
            // Thêm is_active như đã bàn
            $table->boolean('is_active')->default(true)->after('expiry_date');
            
            // Thêm start_date sau expiry_date (vì expiry_date đã có sẵn trong DB cũ)
            $table->timestamp('start_date')->nullable()->after('expiry_date');

            // 2. Xóa cột cũ không dùng nữa
            $table->dropColumn(['discount_rate', 'condition']);
        });
    }

    public function down()
    {
        // Logic rollback nếu cần (thêm lại cột cũ, xóa cột mới)
        Schema::table('vouchers', function (Blueprint $table) {
            $table->double('discount_rate');
            $table->string('condition')->nullable();
            $table->dropColumn(['code', 'name', 'type', 'value', 'max_discount_amount', 'min_order_value', 'quantity', 'used_count', 'is_active', 'start_date']);
        });
    }
};
