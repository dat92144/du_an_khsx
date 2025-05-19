<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersTableSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'id' => 'sup001',
                'name' => 'Nhà Cung Cấp A',
                'contact_info' => 'Email: supplierA@example.com, Phone: 0123456789',
            ],
            [
                'id' => 'sup002',
                'name' => 'Nhà Cung Cấp B',
                'contact_info' => 'Email: supplierB@example.com, Phone: 0987654321',
            ],
            [
                'id' => 'sup003',
                'name' => 'Nhà Cung Cấp C',
                'contact_info' => 'Email: supplierC@example.com, Phone: 0345678912',
            ],
            [
                'id' => 'sup004',
                'name' => 'Nhà cung cấp SFP001',
                'contact_info' => 'Email: sfp001@supplier.com, Phone: 0111222333',
            ],
            [
                'id' => 'sup005',
                'name' => 'Nhà cung cấp MAT005',
                'contact_info' => 'Email: mat005@supplier.com, Phone: 0999888777',
            ],
        ];

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->updateOrInsert(
                ['id' => $supplier['id']],
                array_merge($supplier, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
