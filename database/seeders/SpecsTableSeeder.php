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

            // ===== CLINKER CPC50 (SFP002) =====
            [
                'id' => 'SPEC001',
                'name' => 'Nghiền & phối trộn nguyên liệu clinker',
                'description' => 'Chuẩn bị đá vôi, đất sét, quặng sắt',
                'product_id' => null,
                'semi_finished_product_id' => 'SFP002',
                'process_id' => 'P001',
                'machine_id' => 'MAC002', // Máy nghiền PE-600x900
                'lead_time' => 2.0,
                'cycle_time' => 0.5,
                'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SPEC002',
                'name' => 'Nung clinker',
                'description' => 'Nung phối liệu đã nghiền ở 1450°C',
                'product_id' => null,
                'semi_finished_product_id' => 'SFP002',
                'process_id' => 'P002',
                'machine_id' => 'MAC005', // Lò quay FL Smidth
                'lead_time' => 3.0,
                'cycle_time' => 1.0,
                'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SPEC003',
                'name' => 'Làm mát clinker',
                'description' => 'Giảm nhiệt clinker sau nung',
                'product_id' => null,
                'semi_finished_product_id' => 'SFP002',
                'process_id' => 'P003',
                'machine_id' => 'MAC006', // Máy làm mát KHD Grate Cooler
                'lead_time' => 0.5,
                'cycle_time' => 0.2,
                'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ===== XI MĂNG RỜI PCB40 (SFP001) =====
            [
                'id' => 'SPEC004',
                'name' => 'Nghiền clinker tạo xi măng rời',
                'description' => 'Nghiền clinker + phụ gia',
                'product_id' => null,
                'semi_finished_product_id' => 'SFP001',
                'process_id' => 'P004',
                'machine_id' => 'MAC007', // Máy nghiền bi
                'lead_time' => 2.0,
                'cycle_time' => 0.7,
                'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SPEC005',
                'name' => 'Phân loại và kiểm tra xi măng rời',
                'description' => 'Đảm bảo xi măng đạt chất lượng',
                'product_id' => null,
                'semi_finished_product_id' => 'SFP001',
                'process_id' => 'P005',
                'machine_id' => 'MAC008', // Bộ phân ly O-SEPA
                'lead_time' => 1.0,
                'cycle_time' => 0.5,
                'lot_size' => 100,
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ===== XI MĂNG BAO PCB40 (PRO001) =====
            [
                'id' => 'SPEC006',
                'name' => 'Đóng bao xi măng PCB40',
                'description' => 'Đóng gói xi măng rời thành bao 50kg',
                'product_id' => 'PRO001',
                'semi_finished_product_id' => null,
                'process_id' => 'P006',
                'machine_id' => 'MAC009', // Máy đóng bao
                'lead_time' => 1.5,
                'cycle_time' => 0.4,
                'lot_size' => 2000,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'SPEC007',
                'name' => 'Xuất kho xi măng PCB40',
                'description' => 'Vận chuyển đến khách hàng',
                'product_id' => 'PRO001',
                'semi_finished_product_id' => null,
                'process_id' => 'P007',
                'machine_id' => 'MAC010', // Xe tải Hyundai HD320
                'lead_time' => 0.5,
                'cycle_time' => 0.2,
                'lot_size' => 2000,
                'created_at' => now(), 'updated_at' => now(),
            ],

        ]);

    }
}
