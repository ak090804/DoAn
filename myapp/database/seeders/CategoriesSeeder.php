<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Đồ uống có cồn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trẻ sơ sinh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bánh ngọt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nước giải khát',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bữa sáng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nguyên liệu số lượng lớn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đồ hộp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trứng, sữa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đồ nguội',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mì',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đồ gia dụng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thịt & hải sản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nguyên liệu nấu ăn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chăm sóc cá nhân',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thú cưng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nông sản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đồ ăn nhẹ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
