<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SupplierPricesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('supplier_prices')->insert([
            [
                'id' => 'sp001', 'supplier_id' => 'sup001', 'material_id' => 'mat001',
                'price_per_unit' => 50.5, 'unit_id' => 'u001', 'effective_date' => now(),
                'delivery_time' => '5',
            ],
            [
                'id' => 'sp002', 'supplier_id' => 'sup002', 'material_id' => 'mat002',
                'price_per_unit' => 75.0, 'unit_id' => 'u001', 'effective_date' => now(),
                'delivery_time' => '7',
            ],
            [
                'id' => 'sp003', 'supplier_id' => 'sup003', 'material_id' => 'mat003',
                'price_per_unit' => 120.3, 'unit_id' => 'u002', 'effective_date' => now(),
                'delivery_time' => '3',
            ],
            
        ]);
    }
}
