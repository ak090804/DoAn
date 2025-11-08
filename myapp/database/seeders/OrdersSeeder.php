<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        $orders = [
            [
                'customers_id' => 1,
                'total_price' => 100000,
                'status' => 'pending',
                'note' => 'Giao giờ hành chính',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customers_id' => 2,
                'total_price' => 250000,
                'status' => 'confirmed',
                'note' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('orders')->insert($orders);
    }
}
