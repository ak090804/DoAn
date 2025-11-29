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
        // Safer approach to add 'paid' to the enum:
        // 1) Convert to VARCHAR to avoid ALTER ENUM truncation errors
        // 2) Normalize unexpected values to 'pending'
        // 3) Convert to ENUM including 'paid'

        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` NOT IN ('pending','approved','cancelled','paid') OR `status` IS NULL");
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','approved','cancelled','paid') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Keep rollback safe by converting to VARCHAR and normalizing values.
        // We avoid converting back to a smaller ENUM to prevent truncation warnings.
        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` NOT IN ('pending','approved','cancelled') OR `status` IS NULL");
    }
};
