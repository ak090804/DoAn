<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'), // hash mật khẩu
                'name' => 'Administrator',
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'customer1@example.com',
                'password' => Hash::make('customer123'),
                'name' => 'Nguyen Van A',
                'role' => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'customer2@example.com',
                'password' => Hash::make('customer123'),
                'name' => 'Tran Thi B',
                'role' => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
