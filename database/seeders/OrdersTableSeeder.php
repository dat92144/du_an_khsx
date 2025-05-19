<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('orders')->insert([
            [
                'id' => 'o001', 'customer_id' => 'cus001',
                'order_date' => now()->subDays(10), 'delivery_date' => now()->addDays(5),
                'status' => 'pending',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'o002', 'customer_id' => 'cus002',
                'order_date' => now()->subDays(7), 'delivery_date' => now()->addDays(3),
                'status' => 'pending',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
