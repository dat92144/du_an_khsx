<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class InventoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventories')->insert([
            [
                'id' => 'i001', 'item_id' => 'mat001', 'item_type' => 'material',
                'quantity' => 500, 'unit_id' => 'u001', 'last_updated' => now(),
            ],
            [
                'id' => 'i002', 'item_id' => 'mat002', 'item_type' => 'semi_finished_product',
                'quantity' => 100, 'unit_id' => 'u001', 'last_updated' => now(),
            ],
            [
                'id' => 'i003', 'item_id' => 'mat003', 'item_type' => 'product',
                'quantity' => 50, 'unit_id' => 'U002', 'last_updated' => now(),
            ],
            [
                'id' => 'i004', 'item_id' => 'mat004', 'item_type' => 'product',
                'quantity' => 50, 'unit_id' => 'U003', 'last_updated' => now(),
            ],
            [
                'id' => 'i005', 'item_id' => 'mat005', 'item_type' => 'product',
                'quantity' => 50, 'unit_id' => 'U004', 'last_updated' => now(),
            ],
        ]);
    }
}
