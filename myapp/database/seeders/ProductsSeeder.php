<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Thực phẩm
            [
                'name' => 'Gạo',
                'category_id' => 1
            ],
            [
                'name' => 'Rau xanh',
                'category_id' => 1
            ],
            // Đồ uống
            [
                'name' => 'Nước ép trái cây',
                'category_id' => 2
            ],
            [
                'name' => 'Trà xanh',
                'category_id' => 2
            ],
            // Đồ gia dụng
            [
                'name' => 'Chảo chống dính',
                'category_id' => 3
            ],
            [
                'name' => 'Bàn chải đánh răng',
                'category_id' => 3
            ],
        ];

        DB::table('products')->insert($products);
    }
}
