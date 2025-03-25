<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'id' => 'Per001',
                'name' => 'manage_users',
                'description' => 'Quản lý người dùng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'Per002',
                'name' => 'manage_orders',
                'description' => 'Quản lý đơn hàng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'Per003',
                'name' => 'view_reports',
                'description' => 'Xem báo cáo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
