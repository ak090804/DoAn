<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add 'confirmed' and 'shipping' (đang giao) to orders.status enum
        // Using raw SQL because altering enum via Blueprint is limited
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','approved','confirmed','shipping','completed','cancelled') NOT NULL DEFAULT 'pending';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original set (keep 'approved' and 'cancelled' and 'pending')
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','approved','cancelled') NOT NULL DEFAULT 'pending';");
    }
};
