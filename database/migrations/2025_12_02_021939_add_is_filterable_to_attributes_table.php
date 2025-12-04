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
        Schema::table('attributes', function (Blueprint $table) {
            // Mặc định là false (0), admin thích thì bật lên
            $table->boolean('is_filterable')->default(false)->after('unit'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
        $table->dropColumn('is_filterable');
    });
    }
};
