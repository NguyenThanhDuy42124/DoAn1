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
       Schema::create('followers', function (Blueprint $table) {
        // ID của người theo dõi (follower)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        // ID của người bán được theo dõi (seller)
        $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
        
        // Đặt khóa chính gộp
        $table->primary(['user_id', 'seller_id']);
    });
    }

    public function down()
    {
        Schema::dropIfExists('followers');
    }
};
