<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'id' => 'sup001', 'name' => 'Nhà Cung Cấp A',
                'contact_info' => 'Email: supplierA@example.com, Phone: 0123456789',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'sup002', 'name' => 'Nhà Cung Cấp B',
                'contact_info' => 'Email: supplierB@example.com, Phone: 0987654321',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'sup003', 'name' => 'Nhà Cung Cấp C',
                'contact_info' => 'Email: supplierC@example.com, Phone: 0345678912',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
