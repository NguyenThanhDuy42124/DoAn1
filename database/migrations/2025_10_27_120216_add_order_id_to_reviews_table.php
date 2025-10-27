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
        Schema::table('reviews', function (Blueprint $table) {
            // Thêm cột order_id, đặt sau product_id
            $table->foreignId('order_id')
                  ->nullable() // Cho phép null nếu bạn có review cũ
                  ->after('product_id')
                  ->constrained('orders') // Khóa ngoại tới bảng 'orders'
                  ->onDelete('set null'); // Nếu xóa đơn hàng thì giữ lại review
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
