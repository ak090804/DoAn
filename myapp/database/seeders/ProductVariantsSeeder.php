<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_variants')->insert([
            // 1. Gạo
            [
                'product_id' => 1,
                'brand' => 'Hà Nội Rice',
                'attribute' => '5kg',
                'description' => 'Gạo dẻo thơm, túi 5kg.',
                'price' => 150000,
                'image' => 'gao_5kg.png',
                'quantity' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'brand' => 'Hà Nội Rice',
                'attribute' => '10kg',
                'description' => 'Gạo dẻo thơm, túi 10kg tiết kiệm.',
                'price' => 280000,
                'image' => 'gao_10kg.png',
                'quantity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2. Rau xanh
            [
                'product_id' => 2,
                'brand' => 'EcoFarm',
                'attribute' => '500g',
                'description' => 'Rau muống tươi 500g.',
                'image' => 'raumuong.png',
                'price' => 20000,
                'quantity' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'brand' => 'EcoFarm',
                'attribute' => '1kg',
                'description' => 'Rau cải xanh hữu cơ 1kg.',
                'price' => 35000,
                'image' => 'raucaixanh.png',
                'quantity' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 3. Nước ép trái cây
            [
                'product_id' => 3,
                'brand' => 'Tropicana',
                'attribute' => '330ml',
                'description' => 'Nước ép cam tươi Tropicana 330ml.',
                'price' => 18000,
                'image' => 'nuocep_cam.png',
                'quantity' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 3,
                'brand' => 'Tropicana',
                'attribute' => '1L',
                'description' => 'Nước ép táo nguyên chất 1L.',
                'price' => 42000,
                'image' => 'nuocep_tao.png',
                'quantity' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 4. Trà xanh
            [
                'product_id' => 4,
                'brand' => 'Lipton',
                'attribute' => '20 túi',
                'description' => 'Trà xanh Lipton hộp 20 túi lọc.',
                'price' => 35000,
                'image' => 'traxanh_20.png',
                'quantity' => 250,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 4,
                'brand' => 'C2',
                'attribute' => '500ml',
                'description' => 'Trà xanh đóng chai C2 500ml.',
                'price' => 10000,
                'image' => 'tra_c2.png',
                'quantity' => 400,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 5. Chảo chống dính
            [
                'product_id' => 5,
                'brand' => 'Sunhouse',
                'attribute' => '26cm',
                'description' => 'Chảo chống dính Sunhouse 26cm.',
                'price' => 185000,
                'image' => 'chao_26cm.png',
                'quantity' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 5,
                'brand' => 'HappyCook',
                'attribute' => '30cm',
                'description' => 'Chảo chống dính HappyCook 30cm cao cấp.',
                'price' => 230000,
                'image' => 'chao_30cm.png',
                'quantity' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 6. Bàn chải đánh răng
            [
                'product_id' => 6,
                'brand' => 'P/S',
                'attribute' => '2 chiếc',
                'description' => 'Bàn chải lông mềm P/S (2 chiếc).',
                'price' => 28000,
                'image' => 'banchai_ps.png',
                'quantity' => 180,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 6,
                'brand' => 'Colgate',
                'attribute' => '3 chiếc',
                'description' => 'Bàn chải Colgate mềm mại (3 chiếc).',
                'price' => 42000,
                'image' => 'banchai_colgate.png',
                'quantity' => 140,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
