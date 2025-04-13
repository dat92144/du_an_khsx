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
                'id' => 'P001',
                'name' => 'Nghiền và phối trộn nguyên liệu',
                'description' => 'Chuẩn bị nguyên liệu cho sản xuất clinker',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'P002',
                'name' => 'Nung clinker',
                'description' => 'Nung hỗn hợp nguyên liệu ở nhiệt độ cao để tạo clinker',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'P003',
                'name' => 'Làm mát clinker',
                'description' => 'Làm mát clinker sau khi nung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'P004',
                'name' => 'Nghiền clinker tạo xi măng rời',
                'description' => 'Nghiền clinker + phụ gia tạo xi măng PCB40 dạng rời',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'P005',
                'name' => 'Phân loại và kiểm tra chất lượng xi măng',
                'description' => 'Lọc xi măng đạt tiêu chuẩn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'P006',
                'name' => 'Đóng bao xi măng',
                'description' => 'Đóng bao xi măng PCB40 để tiêu thụ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'P007',
                'name' => 'Xuất kho xi măng',
                'description' => 'Vận chuyển xi măng bao ra thị trường',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
