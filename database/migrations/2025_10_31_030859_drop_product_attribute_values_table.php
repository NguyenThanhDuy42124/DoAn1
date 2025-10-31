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
        // Xóa bảng nếu nó tồn tại
        Schema::dropIfExists('product_attribute_values');
    }

    /**
     * Reverse the migrations.
     * (Để rollback, mày phải tạo lại bảng này. 
     * Mày có thể copy code từ file migration ..._create_product_attribute_values_table.php CŨ dán vào đây)
     */
    public function down(): void
    {
        // Tạm thời để trống, hoặc copy code tạo bảng cũ vào đây
        Schema::create('product_attribute_values', function (Blueprint $table) {
            // ... Copy code từ file migration cũ của mày ...
            // $table->id();
            // $table->foreignId('product_id')->constrained...
            // $table->foreignId('attribute_id')->constrained...
            // $table->string('value');
            // $table->timestamps();
        });
    }
};