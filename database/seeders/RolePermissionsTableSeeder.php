<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RolePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_permissions')->insert([
            [
                'id' => '1',
                'role_id' => 'admin',
                'permission_id' => 'Per001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '2',
                'role_id' => 'admin',
                'permission_id' => 'Per002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '3',
                'role_id' => 'admin',
                'permission_id' => 'Per003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
