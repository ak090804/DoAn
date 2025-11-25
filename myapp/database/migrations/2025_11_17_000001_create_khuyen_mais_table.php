<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('khuyen_mais', function (Blueprint $table) {
            $table->id();
            $table->string('ma')->nullable()->unique();
            $table->string('ten');
            $table->text('mo_ta')->nullable();
            $table->enum('loai', ['tang_sp', 'giam_gia_sp', 'giam_gia_hd'])->default('giam_gia_hd');
            $table->decimal('gia_tri', 10, 2)->nullable()->comment('Percent or fixed value depending on config');
            $table->decimal('so_tien_giam_toi_da', 12, 2)->nullable();
            $table->boolean('is_private')->default(false);
            $table->json('data')->nullable()->comment('Extra data: product ids, gift product_variant_id, etc.');
            $table->boolean('active')->default(true);
            $table->timestamp('ngay_bat_dau')->nullable();
            $table->timestamp('ngay_ket_thuc')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('khuyen_mais');
    }
};
