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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ekyc_status')->default('not_submitted')->after('status');
            $table->string('cccd_front_image_path')->nullable()->after('ekyc_status');
            $table->string('cccd_back_image_path')->nullable()->after('cccd_front_image_path');
            $table->string('cccd_selfie_image_path')->nullable()->after('cccd_back_image_path');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ekyc_status');
            $table->dropColumn('cccd_front_image_path');
            $table->dropColumn('cccd_back_image_path');
            $table->dropColumn('cccd_selfie_image_path');
        });
    }
};
