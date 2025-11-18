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
        Schema::table('products', function (Blueprint $table) {
            // Thêm cột brand_id
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null')->after('category_id');
            // Xóa cột brand cũ
            if (Schema::hasColumn('products', 'brand')) {
                $table->dropColumn('brand');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Khôi phục cột brand
            $table->string('brand')->nullable()->after('category_id');
            // Xóa cột brand_id
            $table->dropColumn('brand_id');
        });
    }
};
