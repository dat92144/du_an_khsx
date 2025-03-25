<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers')->insert([
            [
                'id' => 'cus001',
                'name' => 'Công ty ABC',
                'description' => 'Khách hàng chuyên nhập khẩu xe đạp thể thao.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'cus002',
                'name' => 'Shop Xe Đạp Thành Đạt',
                'description' => 'Cửa hàng chuyên bán xe đạp cao cấp.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
