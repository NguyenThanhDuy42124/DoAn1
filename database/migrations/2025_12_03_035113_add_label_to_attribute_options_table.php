<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attribute_options', function (Blueprint $table) {
            // Thêm cột label kiểu VARCHAR(255), cho phép NULL, đặt ngay sau cột value
            $table->string('label', 255)->nullable()->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('attribute_options', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }
};