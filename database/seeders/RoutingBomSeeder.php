<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutingBomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('routing_bom')->insert([
            ['id' => 'ROBOM001', 'product_id' => 'pro001', 'step_order' => 1, 'machine_id' => 'mac001', 'process_id' => 'pr001', 'cycle_time' => '14', 'lead_time' => '7', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'ROBOM002', 'product_id' => 'pro001', 'step_order' => 2, 'machine_id' => 'mac002', 'process_id' => 'pr002', 'cycle_time' => '10', 'lead_time' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'ROBOM003', 'product_id' => 'pro002', 'step_order' => 1, 'machine_id' => 'mac001', 'process_id' => 'pr001', 'cycle_time' => '14', 'lead_time' => '7', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'ROBOM004', 'product_id' => 'pro002', 'step_order' => 2, 'machine_id' => 'mac002', 'process_id' => 'pr002', 'cycle_time' => '10', 'lead_time' => '5', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
