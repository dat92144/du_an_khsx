<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductPriceSeeder extends Seeder
{
    public function run(): void
    {
        $expectedProfitPercent = 20;
        $currentYear = now()->year;

        $costs = DB::table('product_costs')->get();

        foreach ($costs as $cost) {
            $sellPrice = $cost->total_cost * (1 + $expectedProfitPercent / 100);

            DB::table('product_prices')->updateOrInsert(
                [
                    'product_id' => $cost->product_id,
                    'year' => $currentYear,
                ],
                [
                    'id' => 'PRICE-' . $cost->product_id,
                    'total_cost' => $cost->total_cost,
                    'expected_profit_percent' => $expectedProfitPercent,
                    'sell_price' => $sellPrice,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
