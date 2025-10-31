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
        Schema::table('categories', function (Blueprint $table) {
            // 1. Xóa khóa ngoại trước
            // Laravel 8+ tự động tìm tên khóa ngoại
            $table->dropForeign(['parent_id']);

            // 2. Xóa 3 cột: description, parent_id, sort_order
            $table->dropColumn(['description', 'parent_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     * (Hàm 'down' để lỡ có lỗi thì 'rollback' lại như cũ)
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->integer('sort_order')->default(0);
        });
    }
};