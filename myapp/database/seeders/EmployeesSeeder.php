<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

use App\Models\User;

class EmployeesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');

        // Clear current employees to have deterministic seed (disable FK checks temporarily)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('employees')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Realistic fixed dataset – 6 employees
        $now = now();
        $dataset = [
            [
                'name' => 'Nguyễn Thị Lan',
                'phone' => '0912345678',
                'address' => 'Số 12, Phố Hàng Bông, Hoàn Kiếm, Hà Nội',
                'position' => 'Thu ngân',
                'salary' => 5200.00,
                'hired_at' => '2021-03-01 08:00:00',
                'email' => 'lan.thu.ngan@example.com',
            ],
            [
                'name' => 'Phạm Văn Dũng',
                'phone' => '0987654321',
                'address' => 'Số 45, Đường Trần Phú, Quận Hà Đông, Hà Nội',
                'position' => 'Thu ngân',
                'salary' => 4800.00,
                'hired_at' => '2022-07-15 09:00:00',
                'email' => 'dung.thu.ngan@example.com',
            ],
            [
                'name' => 'Trương Minh Anh',
                'phone' => '0905123456',
                'address' => 'Số 8, Khu đô thị Vinhomes, TP. Hồ Chí Minh',
                'position' => 'Tiếp thị',
                'salary' => 6000.00,
                'hired_at' => '2020-11-20 09:30:00',
                'email' => 'minh.anh.marketing@example.com',
            ],
            [
                'name' => 'Lê Thị Hồng',
                'phone' => '0933888777',
                'address' => 'Số 101, Đường Lê Lợi, Quận 1, TP. Hồ Chí Minh',
                'position' => 'Tiếp thị',
                'salary' => 5800.00,
                'hired_at' => '2019-05-10 08:30:00',
                'email' => 'hong.marketing@example.com',
            ],
            [
                'name' => 'Hoàng Văn Nam',
                'phone' => '0977111222',
                'address' => 'Số 3, Ngõ 7, Phường Bách Khoa, Hà Nội',
                'position' => 'Kiểm kho',
                'salary' => 5400.00,
                'hired_at' => '2018-09-01 07:45:00',
                'email' => 'nam.kho@example.com',
            ],
            [
                'name' => 'Đặng Thị Mai',
                'phone' => '0966001122',
                'address' => 'Số 77, Phố Nguyễn Trãi, Thanh Xuân, Hà Nội',
                'position' => 'Kiểm kho',
                'salary' => 5300.00,
                'hired_at' => '2023-01-05 08:15:00',
                'email' => 'mai.kho@example.com',
            ],
        ];

        $insert = [];

        foreach ($dataset as $row) {
            $userId = null;

            // Only Thu ngân (cashiers) get a linked user account to allow login later
            if ($row['position'] === 'Thu ngân') {
                // create or reuse a user account with this email
                $user = User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['name'],
                        'password' => Hash::make('password123'),
                        'role' => 'staff',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $userId = $user->id;
                // keep employee email equal to user email
                $email = $user->email;
            } else {
                // non-linked employees: keep provided email but do not create user
                $email = $row['email'];
            }

            $insert[] = [
                'user_id' => $userId,
                'name' => $row['name'],
                'email' => $email,
                'phone' => $row['phone'],
                'address' => $row['address'],
                'position' => $row['position'],
                'salary' => $row['salary'],
                'hired_at' => $row['hired_at'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('employees')->insert($insert);
    }
}
