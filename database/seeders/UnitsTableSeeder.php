<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            [
                'id' => 'U001',
                'name' => 'Tấn',
                'description' => 'Đơn vị tính chính cho nguyên liệu và sản phẩm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'U002',
                'name' => 'Kilogram',
                'description' => 'Đơn vị tính phổ biến cho đóng bao (50kg, v.v.)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'U003',
                'name' => 'Bao',
                'description' => 'Đơn vị đóng gói sản phẩm PCB40',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
