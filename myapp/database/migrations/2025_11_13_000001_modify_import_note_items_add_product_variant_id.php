<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('import_note_items', function (Blueprint $table) {
            // drop foreign key and product_id column then add product_variant_id
            if (Schema::hasColumn('import_note_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }

            if (!Schema::hasColumn('import_note_items', 'product_variant_id')) {
                $table->foreignId('product_variant_id')->after('import_note_id')->constrained('product_variants')->onDelete('restrict');
            }
        });
    }

    public function down()
    {
        Schema::table('import_note_items', function (Blueprint $table) {
            if (Schema::hasColumn('import_note_items', 'product_variant_id')) {
                $table->dropForeign(['product_variant_id']);
                $table->dropColumn('product_variant_id');
            }

            if (!Schema::hasColumn('import_note_items', 'product_id')) {
                $table->foreignId('product_id')->after('import_note_id')->constrained('products')->onDelete('restrict');
            }
        });
    }
};
