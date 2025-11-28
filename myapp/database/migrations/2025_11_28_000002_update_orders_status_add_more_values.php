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
        // Expand allowed status values safely.
        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");

        // Allowed values used across the app
        $allowed = [
            'pending',
            'pending_payment',
            'ordered',
            'approved',
            'paid',
            'confirmed',
            'completed',
            'cancelled'
        ];

        $inList = implode("','", $allowed);

        // Normalize anything not in allowed set to 'pending'
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` NOT IN ('$inList') OR `status` IS NULL");

        // Convert back to ENUM with expanded values
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('$inList') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to a safe subset (keep paid)
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','approved','cancelled','paid') NOT NULL DEFAULT 'pending'");
    }
};
