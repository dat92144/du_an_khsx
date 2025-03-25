<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProcessesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('processes')->insert([
            [
                'id' => 'pr001',
                'name' => 'Cắt CNC',
                'description' => 'Cắt khung xe từ vật liệu nhôm hoặc carbon bằng máy CNC.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'pr002',
                'name' => 'Hàn khung',
                'description' => 'Hàn tạo khung xe cơ bản.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'pr003',
                'name' => 'Sơn Phủ',
                'description' => 'Sơn chống nước và bảo vệ bề mặt khung xe.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'pr004',
                'name' => 'Lắp Ráp',
                'description' => 'Lắp khung, bánh xe, tay lái và các bộ phận khác để hoàn thành xe đạp.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'pr005',
                'name' => 'Lắp khớp nối',
                'description' => 'Tạo các khớp để gấp xe.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
