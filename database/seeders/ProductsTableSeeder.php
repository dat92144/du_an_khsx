<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            'id' => 'PRO001',
            'name' => 'Xi măng bao PCB40',
            'description' => 'Xi măng đóng bao sau khi kiểm tra chất lượng và đóng gói',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
