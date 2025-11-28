<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apriori_recommendations', function (Blueprint $table) {
            $table->id();

            // Sản phẩm gốc (antecedent)
            $table->unsignedBigInteger('product_id');

            // Sản phẩm được gợi ý (consequent)
            $table->unsignedBigInteger('recommended_product_id');

            // Confidence
            $table->float('confidence')->default(0);

            // Support từ Apriori
            $table->float('support')->default(0);

            // Lift từ Apriori
            $table->float('lift')->default(1);

            $table->timestamps();

            // OPTIONAL: Foreign keys nếu có bảng products
            // $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            // $table->foreign('recommended_product_id')->references('id')->on('products')->cascadeOnDelete();

            // UNIQUE để tránh trùng cặp sản phẩm
            $table->unique(['product_id', 'recommended_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apriori_recommendations');
    }
};
