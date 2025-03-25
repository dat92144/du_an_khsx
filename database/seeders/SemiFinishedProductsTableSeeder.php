<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SemiFinishedProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('semi_finished_products')->insert([
            [
                'id' => 'sem001',
                'name' => 'Khung Xe Nhôm CNC',
                'description' => 'Khung xe đạp đã qua xử lý CNC nhưng chưa sơn.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem002',
                'name' => 'Khung Xe Carbon CNC',
                'description' => 'Khung xe đạp đã qua xử lý CNC nhưng chưa sơn.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem003',
                'name' => 'Bánh Xe Đạp',
                'description' => 'Bánh xe đã lắp sẵn lốp, sẵn sàng cho lắp ráp.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem004',
                'name' => 'Khung Xe Nhôm',
                'description' => 'Khung xe đã được sơn hoàn thiện.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem005',
                'name' => 'Khung Xe Carbon',
                'description' => 'Khung xe đã được sơn hoàn thiện.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem006',
                'name' => 'Bộ đĩa, lips',
                'description' => 'Bộ đĩa lips Shimano đã sẵn sàng lắp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem007',
                'name' => 'Bộ dây phanh',
                'description' => 'Sẵn sàng lắp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem008',
                'name' => ' Nhôm đã cắt',
                'description' => 'Sẵn sàng hàn tạo khung xe cnc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem009',
                'name' => 'Carbon đã cắt',
                'description' => 'Sẵn sàng hàn tạo khung xe cnc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem010',
                'name' => 'Khung xe gấp CNC',
                'description' => 'Khung xe chưa sơn hoàn thiện',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sem011',
                'name' => 'Khung xe gấp',
                'description' => 'Khung xe đã sơn hoàn thiện',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
