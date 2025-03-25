<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UserRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_roles')->insert([
            [
                'id' => '1',
                'user_id' => '1',
                'role_id' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '2',
                'user_id' => '2',
                'role_id' => 'emp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
