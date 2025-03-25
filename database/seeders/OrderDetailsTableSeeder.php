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
                'id' => 'od001', 'order_id' => 'O001', 'product_id' => 'pro001',
                'product_type' => 'product', 'quantity_product' => 10, 'unit_id' => 'U002',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'od002', 'order_id' => 'o001', 'product_id' => 'pro002',
                'product_type' => 'product', 'quantity_product' => 5, 'unit_id' => 'U002',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'od003', 'order_id' => 'o001', 'product_id' => 'pro003',
                'product_type' => 'product', 'quantity_product' => 10, 'unit_id' => 'U002',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'od004', 'order_id' => 'o001', 'product_id' => 'pro004',
                'product_type' => 'product', 'quantity_product' => 5, 'unit_id' => 'U002',
                'created_at' => now(), 'updated_at' => now(),
            ],

            [
                'id' => 'od005', 'order_id' => 'o002', 'product_id' => 'pro003',
                'product_type' => 'product', 'quantity_product' => 10, 'unit_id' => 'U002',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'od006', 'order_id' => 'o002', 'product_id' => 'pro004',
                'product_type' => 'product', 'quantity_product' => 5, 'unit_id' => 'U002',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
