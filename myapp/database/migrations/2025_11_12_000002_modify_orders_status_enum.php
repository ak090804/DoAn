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
        // Safely revert to original set without causing truncation:
        // 1) Convert to VARCHAR
        // 2) Normalize unexpected values to 'pending'
        // 3) Convert back to ENUM
        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending';");
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` NOT IN ('pending','approved','cancelled') OR `status` IS NULL");
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','approved','cancelled') NOT NULL DEFAULT 'pending';");
    }
};
