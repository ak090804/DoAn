<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create two demo customers with linked user accounts
        $usersData = [
            [
                'name' => 'Nguyen Van A',
                'email' => 'customer1@example.com',
                'password' => 'customer123',
            ],
            [
                'name' => 'Tran Thi B',
                'email' => 'customer2@example.com',
                'password' => 'customer123',
            ],
        ];

        foreach ($usersData as $u) {
            // create user only if it doesn't exist
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make($u['password']),
                    'role' => 'customer',
                ]
            );

            // create customer row only if not already linked
            $exists = DB::table('customers')->where('user_id', $user->id)->exists();
            if (! $exists) {
                DB::table('customers')->insert([
                    'name' => $u['name'],
                    'phone' => '090' . rand(1000000, 9999999),
                    'address' => 'Hà Nội',
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
