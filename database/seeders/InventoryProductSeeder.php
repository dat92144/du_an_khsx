<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventory_products')->insert([
            ['id' => 'INVP001', 'product_id' => 'pro001', 'quantity' => 10, 'unit_id' => 'u003', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'INVP002', 'product_id' => 'pro002', 'quantity' => 5, 'unit_id' => 'u003', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
