<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cập nhật tất cả giá trị cũ → map sang enum mới
        DB::table('products')->update([
            'status' => DB::raw("
                CASE 
                    WHEN status = 'active' THEN 'Approved'
                    WHEN status = 'inactive' THEN 'Hidden'
                    ELSE 'Pending'
                END
            ")
        ]);

        // 2. Đổi kiểu cột thành enum
        Schema::table('products', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Approved', 'Hidden', 'Rejected', 'Deleted'])
                  ->default('Pending')
                  ->change();
        });
    }

    public function down(): void
    {
        // Đổi ngược lại
        Schema::table('products', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });

        // (Tùy chọn) Map ngược lại nếu cần
        DB::table('products')->update([
            'status' => DB::raw("
                CASE 
                    WHEN status = 'Approved' THEN 'active'
                    WHEN status = 'Hidden' THEN 'inactive'
                    ELSE 'active'
                END
            ")
        ]);
    }
};