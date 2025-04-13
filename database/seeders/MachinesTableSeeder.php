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
            [
                'id' => 'MAC001',
                'name' => 'Máy cấp liệu Schenck AccuRate',
                'description' => 'Cấp liệu cho hệ thống sản xuất clinker và xi măng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC002',
                'name' => 'Máy nghiền PE-600x900',
                'description' => 'Nghiền nguyên liệu thô để phối trộn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC003',
                'name' => 'Silo đồng nhất hóa Polysius',
                'description' => 'Đồng nhất hóa nguyên liệu trước khi nung clinker',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC004',
                'name' => 'Máy sấy FCB',
                'description' => 'Sấy khô liệu trước khi nung clinker',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC005',
                'name' => 'Lò quay FL Smidth',
                'description' => 'Nung clinker ở nhiệt độ ~1450°C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC006',
                'name' => 'Máy làm mát KHD Grate Cooler',
                'description' => 'Làm mát clinker sau khi nung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC007',
                'name' => 'Máy nghiền bi FL Smidth UMS',
                'description' => 'Nghiền clinker với phụ gia để tạo xi măng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC008',
                'name' => 'Bộ phân ly O-SEPA',
                'description' => 'Phân loại xi măng mịn đạt tiêu chuẩn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC009',
                'name' => 'Máy đóng bao Haver & Boecker',
                'description' => 'Đóng bao xi măng PCB40 (50kg)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAC010',
                'name' => 'Xe tải Hyundai HD320',
                'description' => 'Vận chuyển sản phẩm xi măng ra khỏi nhà máy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
