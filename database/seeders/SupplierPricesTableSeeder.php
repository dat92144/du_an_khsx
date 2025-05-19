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
                'id' => 'sp001',
                'supplier_id' => 'sup001',
                'material_id' => 'MAT001', // phải trùng với seeder
                'price_per_unit' => 50.5,
                'unit_id' => 'u001',
                'effective_date' => now(),
                'delivery_time' => 5,
            ],
            [
                'id' => 'sp002',
                'supplier_id' => 'sup002',
                'material_id' => 'MAT002',
                'price_per_unit' => 75.0,
                'unit_id' => 'u001',
                'effective_date' => now(),
                'delivery_time' => 7,
            ],
            [
                'id' => 'sp003',
                'supplier_id' => 'sup003',
                'material_id' => 'MAT003',
                'price_per_unit' => 120.3,
                'unit_id' => 'u002',
                'effective_date' => now(),
                'delivery_time' => 3,
            ],
            [
                'id' => 'sp004',
                'supplier_id' => 'sup004',
                'material_id' => 'MAT004', // ví dụ bạn định nghĩa SFP001, thì phải thêm vào bảng materials nếu còn dùng FK
                'price_per_unit' => 200.0,
                'unit_id' => 'u003',
                'effective_date' => now(),
                'delivery_time' => 4,
            ],
            [
                'id' => 'sp005',
                'supplier_id' => 'sup005',        // ✅ OK
                'material_id' => 'MAT005',
                'price_per_unit' => 60.0,
                'unit_id' => 'u001',
                'effective_date' => now(),
                'delivery_time' => 4,
            ],
        ]);
    }
}
