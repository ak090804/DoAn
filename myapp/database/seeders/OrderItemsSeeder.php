<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemsSeeder extends Seeder
{
    public function run()
    {
        // Các item của đơn hàng (tham chiếu đến order_id và product_id thực tế)
        $orderItems = [
            // Đơn hàng 1 (customers_id = 1)
            [
                'order_id' => 1,
                'product_id' => 1, // Gạo Jasmine
                'quantity' => 2,
                'price' => 50000,
                'subtotal' => 2 * 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 1,
                'product_id' => 2, // Rau xanh
                'quantity' => 3,
                'price' => 15000,
                'subtotal' => 3 * 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Đơn hàng 2 (customers_id = 2)
            [
                'order_id' => 2,
                'product_id' => 3, // Nước ép trái cây
                'quantity' => 5,
                'price' => 20000,
                'subtotal' => 5 * 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 2,
                'product_id' => 4, // Trà xanh
                'quantity' => 2,
                'price' => 12000,
                'subtotal' => 2 * 12000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('order_items')->insert($orderItems);
    }
}
