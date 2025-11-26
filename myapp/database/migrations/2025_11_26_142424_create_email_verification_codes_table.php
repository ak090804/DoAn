<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique(); // Email cần xác thực
            $table->string('code'); // Mã xác thực
            $table->integer('attempts')->default(0); // Số lần nhập sai
            $table->timestamp('expires_at')->nullable(); // Hết hạn sau 15 phút
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_verification_codes');
    }
};
