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
                'name' => 'Thực phẩm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đồ uống',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đồ gia dụng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
