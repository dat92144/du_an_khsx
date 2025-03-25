<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class MaterialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('materials')->insert([
            [
                'id' => 'mat001',
                'name' => 'Nhôm Hợp Kim 6061',
                'description' => 'Nguyên liệu chính để sản xuất khung xe đạp.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'mat002',
                'name' => 'Carbon Fiber',
                'description' => 'Vật liệu nhẹ, siêu bền cho khung xe đạp đua.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'mat003',
                'name' => 'Sơn',
                'description' => 'Chống xước, chống han dỉ cho bề mặt khung xe',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'mat004',
                'name' => 'Khớp gấp',
                'description' => 'Dùng cho xe đạp gấp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'mat005',
                'name' => 'Pin',
                'description' => 'Dùng cho xe trợ lực điện',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
