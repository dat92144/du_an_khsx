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
            [
                'id' => 'BOM001',
                'name' => 'BOM Clinker CPC50',
                'description' => 'Tạo clinker từ nguyên liệu đá vôi, đất sét, quặng sắt',
                'semi_finished_product_id' => 'SFP002',
                'product_id' => null,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BOM002',
                'name' => 'BOM Xi măng rời PCB40',
                'description' => 'Tạo xi măng rời từ clinker và phụ gia',
                'semi_finished_product_id' => 'SFP001',
                'product_id' => null,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BOM003',
                'name' => 'BOM Xi măng bao PCB40',
                'description' => 'Đóng bao xi măng rời thành xi măng PCB40',
                'semi_finished_product_id' => null,
                'product_id' => 'PRO001',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

    }
}
