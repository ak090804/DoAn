<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemsSeeder extends Seeder
{
    public function run()
    {
        // Các item của đơn hàng (tham chiếu đến order_id và product_variant_id thực tế)
        $orderItems = [
            // Đơn hàng 1 (customers_id = 1)
            [
                'order_id' => 1,
                'product_variant_id' => 1, // Gạo Hà Nội Rice 5kg
                'quantity' => 2,
                'price' => 50000,
                'subtotal' => 2 * 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 1,
                'product_variant_id' => 3, // Rau EcoFarm 500g
                'quantity' => 3,
                'price' => 15000,
                'subtotal' => 3 * 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Đơn hàng 2 (customers_id = 2)
            [
                'order_id' => 2,
                'product_variant_id' => 5, // Nước ép Tropicana 330ml
                'quantity' => 5,
                'price' => 20000,
                'subtotal' => 5 * 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 2,
                'product_variant_id' => 7, // Trà xanh Lipton 20 túi
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
