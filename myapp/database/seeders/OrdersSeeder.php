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
                    'customer_id' => 1,
                    'employee_id' => 1,
                    'total_price' => 100000,
                    'status' => 'pending',
                    'note' => 'Giao giờ hành chính',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'customer_id' => 2,
                    'employee_id' => 1,
                    'total_price' => 250000,
                    'status' => 'pending',
                    'note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
        ];

        DB::table('orders')->insert($orders);
    }
}
