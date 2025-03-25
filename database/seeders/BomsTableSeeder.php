<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class BomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('boms')->insert([
            ['id' => 'BOM001', 'name' => 'BOM Xe Đạp Địa Hình', 'description' => 'Cấu trúc BOM cho xe đạp địa hình.', 'product_id' => 'pro001', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'BOM002', 'name' => 'BOM Xe Đạp Đua', 'description' => 'Cấu trúc BOM cho xe đạp tốc độ cao.', 'product_id' => 'pro002', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'BOM003', 'name' => 'BOM Xe Đạp Điện', 'description' => 'Cấu trúc BOM cho xe đạp điện trợ lực.', 'product_id' => 'pro003', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'BOM004', 'name' => 'BOM Xe Đạp Gấp', 'description' => 'Cấu trúc BOM cho xe đạp gấp.', 'product_id' => 'pro004', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
