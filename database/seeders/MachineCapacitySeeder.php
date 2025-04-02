<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineCapacitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('machine_capacity')->insert([
            ['id' => 'MACH001', 'machine_id' => 'mac001', 'max_output_per_day' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'MACH002', 'machine_id' => 'mac002', 'max_output_per_day' => 50,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
