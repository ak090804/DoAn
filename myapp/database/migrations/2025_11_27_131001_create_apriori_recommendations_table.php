<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('apriori_recommendations')) {
            Schema::create('apriori_recommendations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('recommended_product_id')->index();
                $table->double('score')->default(0); // confidence
                $table->timestamps();

                // Unique index để tránh trùng product → recommended
                $table->unique(['product_id', 'recommended_product_id'], 'apriori_product_rec_uq');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('apriori_recommendations');
    }
};
