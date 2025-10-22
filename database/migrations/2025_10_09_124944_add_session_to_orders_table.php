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
        if(!Schema::hasColumn('orders', 'session_id'))
        {
            Schema::table('orders', function (Blueprint $table) {
            $table->string('session_id')->nullable()->after('buyer_phone');
        });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'session_id')) {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
    }
    }
};
