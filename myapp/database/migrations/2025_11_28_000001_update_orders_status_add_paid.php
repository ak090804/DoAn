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
        // Safer approach:
        // 1) Modify column to VARCHAR to avoid enum conversion errors when existing rows contain unexpected values
        // 2) Normalize any unexpected status values to 'pending'
        // 3) Convert column to ENUM including 'paid'

        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");

        // Normalize: any status not in allowed set -> set to 'pending'
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` NOT IN ('pending','approved','cancelled','paid') OR `status` IS NULL");

        // Now safely change to ENUM including 'paid'
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','approved','cancelled','paid') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to previous enum set
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','approved','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
