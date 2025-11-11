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
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the old product_id foreign key
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            
            // Add the new product_variant_id foreign key
            $table->foreignId('product_variant_id')->after('order_id')->constrained('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the product_variant_id foreign key
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
            
            // Add back the product_id foreign key
            $table->foreignId('product_id')->after('order_id')->constrained('products')->onDelete('cascade');
        });
    }
};
