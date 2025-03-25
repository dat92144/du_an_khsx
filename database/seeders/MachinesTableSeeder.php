<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class MachinesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('machines')->insert([
            ['id' => 'mac001', 'name' => 'Máy CNC 01', 'description' => 'Gia công cắt khung nhôm chính xác cao.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'mac002', 'name' => 'Máy Sơn Tĩnh Điện', 'description' => 'Sơn chống nước và bảo vệ bề mặt khung xe.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'mac003', 'name' => 'Máy Hàn Laser', 'description' => 'Hàn khung xe bằng công nghệ laser siêu chính xác.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'mac004', 'name' => 'Dây Chuyền Lắp Ráp', 'description' => 'Tự động lắp ráp các bộ phận xe đạp.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'mac006', 'name' => 'Máy Kiểm Tra Chất Lượng', 'description' => 'Kiểm tra và kiểm định chất lượng sản phẩm.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'mac005', 'name' => 'Máy hàn khớp nối', 'description' => 'Gắn các khớp gấp cho xe gấp', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
