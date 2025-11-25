<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('khuyen_mai_id')->nullable()->after('customer_id');
            $table->foreign('khuyen_mai_id')->references('id')->on('khuyen_mais')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['khuyen_mai_id']);
            $table->dropColumn('khuyen_mai_id');
        });
    }
};
