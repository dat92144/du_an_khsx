<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'id' => 'admin',
                'name' => 'Admin',
                'description' => 'Quản trị viên hệ thống',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'emp',
                'name' => 'Employee',
                'description' => 'Nhân viên sản xuất',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'cus',
                'name' => 'Customer',
                'description' => 'Khách hàng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'sup',
                'name' => 'Supplier',
                'description' => 'Nhà cung cấp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
