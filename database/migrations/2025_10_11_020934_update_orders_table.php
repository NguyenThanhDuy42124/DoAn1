<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Đổi tên cột buyer_id thành user_id
            $table->renameColumn('buyer_id', 'user_id');

            // Cập nhật cột status với các giá trị mới
            $table->string('status')->default('Pending')->change();
            // Lưu ý: Nếu cột status hiện có dữ liệu, cần xử lý trước khi đổi giá trị
            // Ví dụ: Chuyển 'unpaid' thành 'Pending'
            DB::table('orders')->where('status', 'unpaid')->update(['status' => 'Pending']);

            // Thêm cột payment_status
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid')->after('status');

            // Thêm cột product_id
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade')->after('seller_id');

            // Thêm cột tracking_code và cancellation_reason
            $table->string('tracking_code')->nullable()->after('updated_at');
            $table->text('cancellation_reason')->nullable()->after('tracking_code');

            // Cập nhật ràng buộc cho seller_id (nếu cần)
            $table->foreignId('seller_id')->nullable()->change()->constrained('users')->onDelete('set null');

            // Thêm index để tối ưu truy vấn
            $table->index('user_id');
            $table->index('seller_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Đổi lại tên cột user_id thành buyer_id
            $table->renameColumn('user_id', 'buyer_id');

            // Xóa các cột mới
            $table->dropColumn(['payment_status', 'product_id', 'tracking_code', 'cancellation_reason']);

            // Đổi lại cột status
            $table->string('status')->default('unpaid')->change();

            // Xóa index
            $table->dropIndex(['user_id', 'seller_id', 'status']);
        });
    }
};