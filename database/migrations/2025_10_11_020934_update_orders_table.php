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
        // Chỉ thực hiện các thao tác liên quan đến user_id nếu cột này CHƯA tồn tại
        if (!Schema::hasColumn('orders', 'user_id')) {
            // Nếu có buyer_id thì đổi tên nó
            if (Schema::hasColumn('orders', 'buyer_id')) {
                $table->renameColumn('buyer_id', 'user_id');
            } else {
                // Nếu không có cả buyer_id và user_id thì mới thêm mới
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->after('id');
            }
        }

        // Cập nhật cột status với các giá trị mới
        // Lưu ý: Lệnh `change()` yêu cầu cài package `doctrine/dbal`
        // composer require doctrine/dbal
        if (Schema::hasColumn('orders', 'status')) {
            $table->string('status')->default('Pending')->change();
            // Cập nhật dữ liệu cũ
            DB::table('orders')->where('status', 'unpaid')->update(['status' => 'Pending']);
        }

        // Thêm cột payment_status nếu chưa có
        if (!Schema::hasColumn('orders', 'payment_status')) {
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid')->after('status');
        }

        // Thêm cột product_id nếu chưa có
        if (!Schema::hasColumn('orders', 'product_id')) {
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade')->after('seller_id');
        }

        // Thêm cột tracking_code và cancellation_reason nếu chưa có
        if (!Schema::hasColumn('orders', 'tracking_code')) {
            $table->string('tracking_code')->nullable()->after('updated_at');
        }
        if (!Schema::hasColumn('orders', 'cancellation_reason')) {
            $table->text('cancellation_reason')->nullable()->after('tracking_code');
        }

        // Thêm index
        $table->index('user_id');
        $table->index('seller_id');
        $table->index('status');
    });
}

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Đổi lại tên cột user_id thành buyer_id nếu tồn tại
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->renameColumn('user_id', 'buyer_id');
            }

            // Xóa các cột mới
            $table->dropColumn(['payment_status', 'product_id', 'tracking_code', 'cancellation_reason']);

            // Đổi lại cột status
            $table->string('status')->default('unpaid')->change();

            // Xóa index
            $table->dropIndex(['user_id', 'seller_id', 'status']);
        });
    }
};