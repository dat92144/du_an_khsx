<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySemiProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventory_semi_products')->insert([
            ['id' => 'INVSP001', 'semi_product_id' => 'sem001', 'quantity' => 100, 'unit_id' => 'u003', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'INVSP002', 'semi_product_id' => 'sem002', 'quantity' => 50, 'unit_id' => 'u003', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
