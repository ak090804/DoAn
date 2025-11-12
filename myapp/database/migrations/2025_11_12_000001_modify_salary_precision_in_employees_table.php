<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Increase salary precision to decimal(14,2)
        // Use raw statement to avoid doctrine/dbal dependency
        DB::statement('ALTER TABLE `employees` MODIFY `salary` DECIMAL(14,2) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `employees` MODIFY `salary` DECIMAL(10,2) NULL');
    }
};
