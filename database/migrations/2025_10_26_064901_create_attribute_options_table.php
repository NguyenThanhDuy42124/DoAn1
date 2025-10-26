<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
            $table->string('value'); // Ví dụ: "8GB", "16GB", "S", "M", "L"
            $table->integer('sort_order')->default(0);
            // Không cần timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_options');
    }
};