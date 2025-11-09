<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategoriesSeeder::class,
            ProductsSeeder::class,
            ProductVariantsSeeder::class,
            SuppliersSeeder::class,
            EmployeesSeeder::class,
            UsersSeeder::class,
            CustomersSeeder::class,
            OrdersSeeder::class,
            OrderItemsSeeder::class,
            ImportNotesSeeder::class,
        ]);
    }
}
