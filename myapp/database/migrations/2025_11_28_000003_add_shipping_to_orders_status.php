<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Make column varchar to avoid enum conversion issues
        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");

        // Allowed statuses including 'shipping'
        $allowed = [
            'pending',
            'pending_payment',
            'ordered',
            'approved',
            'paid',
            'confirmed',
            'shipping',
            'completed',
            'cancelled'
        ];

        $inList = implode("','", $allowed);

        // Normalize unknowns to 'pending'
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` NOT IN ('$inList') OR `status` IS NULL");

        // Convert back to ENUM with the expanded set
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('$inList') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert to previous known set (keep shipping removed)
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','pending_payment','ordered','approved','paid','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
