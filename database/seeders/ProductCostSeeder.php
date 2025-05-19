<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCostSeeder extends Seeder
{
    public function run(): void
    {
        $productId = 'PRO001';
        $materialCost = $this->calculateMaterialCostRecursive($productId);

        DB::table('product_costs')->insert([
            'id' => "CPSX01",
            'product_id' => $productId,
            'material_cost' => $materialCost,
            'overhead_cost' => 500000,
            'inventory_cost' => 300000,
            'transportation_cost' => 200000,
            'wastage_cost' => 100000,
            'depreciation_cost' => 150000,
            'service_outsourcing_cost' => 80000,
            'other_costs' => 50000,
            'total_cost' => $materialCost + 1380000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function calculateMaterialCostRecursive($outputId, $outputType = 'products', $multiplier = 1, $visited = [])
{
    $key = $outputType . ':' . $outputId;
    if (in_array($key, $visited)) return 0; // đã tính → bỏ qua

    $visited[] = $key;
    $total = 0;

    $bomItems = DB::table('bom_items')
        ->where('output_id', $outputId)
        ->where('output_type', $outputType)
        ->get();

    foreach ($bomItems as $item) {
        if ($item->input_material_type === 'materials') {
            $price = DB::table('supplier_prices')
                ->where('material_id', $item->input_material_id)
                ->orderByDesc('effective_date')
                ->value('price_per_unit');
            if ($price !== null) {
                $total += $item->quantity_input * $multiplier * $price;
            }
        } elseif ($item->input_material_type === 'semi_finished_products') {
            $total += $this->calculateMaterialCostRecursive(
                $item->input_material_id,
                'semi_finished_products',
                $multiplier * $item->quantity_input,
                $visited // truyền danh sách đã qua
            );
        }
    }

    return $total;
}

}
