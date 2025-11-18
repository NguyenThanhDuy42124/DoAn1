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
        Schema::table('=orders', function (Blueprint $table) {
            Schema::table('orders', function (Blueprint $table) {
            $table->string('buyer_name')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->string('shipping_address')->nullable(); // Or use a JSON column for full address if complex
    });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('=orders', function (Blueprint $table) {
            //
        });
    }
};
