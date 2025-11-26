<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('product_recommendations')) {
            Schema::create('product_recommendations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_variant_id')->index();
                $table->unsignedBigInteger('recommended_variant_id')->index();
                $table->double('score')->default(0);
                $table->timestamps();

                // Explicit, short index name to avoid MySQL identifier length limits
                $table->unique(['product_variant_id', 'recommended_variant_id'], 'pr_variant_rec_uq');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_recommendations');
    }
};
