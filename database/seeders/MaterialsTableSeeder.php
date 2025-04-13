<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class MaterialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('materials')->insert([
            [
                'id' => 'MAT001',
                'name' => 'Đá vôi (Limestone)',
                'description' => 'Nguyên liệu chính để sản xuất clinker',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAT002',
                'name' => 'Đất sét (Clay)',
                'description' => 'Thành phần phối trộn với đá vôi để sản xuất clinker',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAT003',
                'name' => 'Quặng sắt (Iron ore)',
                'description' => 'Chất điều chỉnh thành phần hóa học',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAT004',
                'name' => 'Tro bay (Fly ash)',
                'description' => 'Phụ gia cho xi măng PCB40',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'MAT005',
                'name' => 'Thạch cao (Gypsum)',
                'description' => 'Điều chỉnh thời gian đông kết xi măng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
