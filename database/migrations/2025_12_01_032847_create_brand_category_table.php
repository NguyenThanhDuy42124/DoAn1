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
        Schema::create('brand_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            // Đảm bảo không trùng lặp (1 brand không thể gán 2 lần cho 1 category)
            $table->unique(['category_id', 'brand_id']); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('brand_category');
    }
};
