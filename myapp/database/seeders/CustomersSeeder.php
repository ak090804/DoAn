<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Khách hàng mẫu gắn với user_id đã tồn tại (ví dụ user_id 2 và 3)
        $customers = [
            [
                'name' => 'Nguyen Van A',
                'phone' => '0901234567',
                'address' => 'Hà Nội',
                'user_id' => 2, // gắn với user_id từ bảng users
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tran Thi B',
                'phone' => '0912345678',
                'address' => 'TP.HCM',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customers')->insert($customers);
    }
}
