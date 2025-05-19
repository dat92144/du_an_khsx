<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCostHistorySeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = now()->year;
        $productCosts = DB::table('product_costs')->get();

        foreach ($productCosts as $cost) {
            $old = DB::table('product_cost_histories')
                ->where('product_id', $cost->product_id)
                ->where('year', '<', $currentYear)
                ->orderByDesc('year')
                ->value('total_cost');

            DB::table('product_cost_histories')->updateOrInsert(
                [
                    'product_id' => $cost->product_id,
                    'year' => $currentYear,
                ],
                [
                    'old_total_cost' => $old,
                    'total_cost' => $cost->total_cost,
                    'reason' => $old ? 'Giá cập nhật theo seed mới' : 'Khởi tạo giá ban đầu',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
