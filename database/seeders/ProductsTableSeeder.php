<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'id' => 'pro001',
                'name' => 'Xe Đạp Địa Hình',
                'description' => 'Xe đạp dành cho địa hình gồ ghề, khung nhôm siêu nhẹ.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'pro002',
                'name' => 'Xe Đạp Đua',
                'description' => 'Xe đạp dành cho đường trường, tốc độ cao, khung carbon.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'pro003',
                'name' => 'Xe Đạp Điện',
                'description' => 'Xe đạp điện trợ lực, pin Lithium-ion.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'pro004',
                'name' => 'Xe Đạp Gấp',
                'description' => 'Xe đạp gấp gọn, tiện lợi di chuyển trong đô thị.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
