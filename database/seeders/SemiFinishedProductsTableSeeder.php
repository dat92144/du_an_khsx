<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SemiFinishedProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('semi_finished_products')->insert([
            [
                'id' => 'SFP001',
                'name' => 'Xi măng rời PCB40',
                'description' => 'Xi măng rời trước khi đóng bao',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'SFP002',
                'name' => 'Clinker CPC50',
                'description' => 'Clinker sau quá trình làm mát và lưu kho',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
