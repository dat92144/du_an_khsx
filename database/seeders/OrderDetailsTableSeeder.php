<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class OrderDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('order_details')->insert([
            [
                'id' => 'od001', 'order_id' => 'o001', 'product_id' => 'PRO001',
                'product_type' => 'product', 'quantity_product' => 10, 'unit_id' => 'u002',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
