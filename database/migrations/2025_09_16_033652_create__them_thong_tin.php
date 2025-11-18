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
        $table->string('phoneNumber', 15)->nullable()->after('name'); // cột số điện thoại
        $table->string('gender')->nullable()->after('phoneNumber');   // cột giới tính
        $table->date('dateOfBirth')->nullable()->after('gender');    // cột ngày sinh
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender');
                $table->dropColumn('phoneNumber');
                $table->dropColumn('dateOfBirth');
            });
    }
};
