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
            ['id' => 'u001', 'name' => 'Kg', 'description' => 'Kilogram - Đơn vị đo trọng lượng.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'u002', 'name' => 'Cái', 'description' => 'Chiếc - Đơn vị đếm sản phẩm hoàn chỉnh.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'u003', 'name' => 'Lít', 'description' => 'Đơn vị đo thể tích (sơn, dầu, chất lỏng).', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'u004', 'name' => 'Bộ', 'description' => 'Bộ phận hoàn chỉnh như bánh xe, truyền động.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
