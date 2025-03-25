<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SpecsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('specs')->insert([
            //san pham 1
            [
                'id' => 'SP001', 'name' => 'Cắt CNC Khung Xe', 'description' => 'Gia công cắt khung xe bằng máy CNC.',
                'product_id' => 'pro001', 'process_id' => 'pr001', 'machine_id' => 'mac001',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP002', 'name' => 'Hàn khung', 'description' => 'Hàn khung xe',
                'product_id' => 'pro001', 'process_id' => 'pr002', 'machine_id' => 'mac003',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP003', 'name' => 'Sơn', 'description' => 'Sơn chống xước.',
                'product_id' => 'pro001', 'process_id' => 'pr003', 'machine_id' => 'mac002',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP004', 'name' => 'Lắp ráp', 'description' => 'Hoàn thiện xe',
                'product_id' => 'pro001', 'process_id' => 'pr004', 'machine_id' => 'mac004',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            // san pham 2
            [
                'id' => 'SP005', 'name' => 'Cắt CNC Khung Xe', 'description' => 'Gia công cắt khung xe bằng máy CNC.',
                'product_id' => 'pro002', 'process_id' => 'pr001', 'machine_id' => 'mac001',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP006', 'name' => 'Hàn khung', 'description' => 'Hàn khung xe',
                'product_id' => 'pro002', 'process_id' => 'pr002', 'machine_id' => 'mac003',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP007', 'name' => 'Sơn', 'description' => 'Sơn chống xước.',
                'product_id' => 'pro002', 'process_id' => 'pr003', 'machine_id' => 'mac002',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP008', 'name' => 'Lắp ráp', 'description' => 'Hoàn thiện xe',
                'product_id' => 'pro002', 'process_id' => 'pr004', 'machine_id' => 'mac004',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            // san pham 3
            [
                'id' => 'SP009', 'name' => 'Cắt CNC Khung Xe', 'description' => 'Gia công cắt khung xe bằng máy CNC.',
                'product_id' => 'pro003', 'process_id' => 'pr001', 'machine_id' => 'mac001',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP010', 'name' => 'Hàn khung', 'description' => 'Hàn khung xe',
                'product_id' => 'pro003', 'process_id' => 'pr002', 'machine_id' => 'mac003',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP011', 'name' => 'Sơn', 'description' => 'Sơn chống xước.',
                'product_id' => 'pro003', 'process_id' => 'pr003', 'machine_id' => 'mac002',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP012', 'name' => 'Lắp ráp', 'description' => 'Hoàn thiện xe',
                'product_id' => 'pro003', 'process_id' => 'pr004', 'machine_id' => 'mac004',
                'lead_time' => 1.5, 'cycle_time' => 20, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            // san pham 3
            [
                'id' => 'SP013', 'name' => 'Cắt CNC Khung Xe', 'description' => 'Gia công cắt khung xe bằng máy CNC.',
                'product_id' => 'pro004', 'process_id' => 'pr001', 'machine_id' => 'mac001',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP014', 'name' => 'Hàn khung', 'description' => 'Hàn khung xe',
                'product_id' => 'pro004', 'process_id' => 'pr002', 'machine_id' => 'mac003',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP015', 'name' => 'Tạo khớp gấp', 'description' => 'Tạo khớp gấp xe',
                'product_id' => 'pro004', 'process_id' => 'pr005', 'machine_id' => 'mac005',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],

            [
                'id' => 'SP016', 'name' => 'Sơn', 'description' => 'Sơn chống xước.',
                'product_id' => 'pro004', 'process_id' => 'pr003', 'machine_id' => 'mac002',
                'lead_time' => 1.5, 'cycle_time' => 10, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SP017', 'name' => 'Lắp ráp', 'description' => 'Hoàn thiện xe',
                'product_id' => 'pro004', 'process_id' => 'pr004', 'machine_id' => 'mac004',
                'lead_time' => 1.5, 'cycle_time' => 20, 'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
