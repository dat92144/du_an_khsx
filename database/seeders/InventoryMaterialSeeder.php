<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventory_materials')->insert([
            ['id' => 'INV001', 'material_id' => 'mat001', 'quantity' => 500, 'unit_id' => 'u001', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'INV002', 'material_id' => 'mat002', 'quantity' => 300, 'unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
