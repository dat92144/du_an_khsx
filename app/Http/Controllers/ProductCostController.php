<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCost;
use App\Models\ProductCostHistory;
use Carbon\Carbon;

class ProductCostController extends Controller
{
    public function index()
    {
        return response()->json(ProductCost::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|unique:product_costs,id',
            'product_id' => 'required|exists:products,id',
            'overhead_cost' => 'required|numeric',
            'inventory_cost' => 'required|numeric',
            'transportation_cost' => 'required|numeric',
            'wastage_cost' => 'required|numeric',
            'depreciation_cost' => 'required|numeric',
            'service_outsourcing_cost' => 'required|numeric',
            'other_costs' => 'required|numeric'
        ]);

        $data = $request->all();
        $data['material_cost'] = $this->calculateMaterialCostRecursive($data['product_id']);
        logger("Chi phí nguyên vật liệu cho sản phẩm {$data['product_id']}: " . $data['material_cost']);

        $data['total_cost'] = $data['material_cost'] + array_sum(array_map('floatval', [
            $data['overhead_cost'],
            $data['inventory_cost'],
            $data['transportation_cost'],
            $data['wastage_cost'],
            $data['depreciation_cost'],
            $data['service_outsourcing_cost'],
            $data['other_costs']
        ]));

        $cost = ProductCost::create($data);

        ProductCostHistory::create([
            'product_id' => $data['product_id'],
            'year' => Carbon::now()->year,
            'old_total_cost' => null,
            'total_cost' => $data['total_cost'],
            'reason' => 'Khởi tạo mới'
        ]);

        return response()->json(['message' => 'Tính chi phí sản xuất thành công', 'data' => $cost]);
    }

    public function show($id)
    {
        return response()->json(ProductCost::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $cost = ProductCost::findOrFail($id);
        $request->validate([
            'id' => 'required|string|unique:product_costs,id',
            'product_id' => 'required|exists:products,id',
            'overhead_cost' => 'required|numeric',
            'inventory_cost' => 'required|numeric',
            'transportation_cost' => 'required|numeric',
            'wastage_cost' => 'required|numeric',
            'depreciation_cost' => 'required|numeric',
            'service_outsourcing_cost' => 'required|numeric',
            'other_costs' => 'required|numeric'
        ]);
        $data = $request->all();

        $oldTotal = $cost->total_cost;

        $data['material_cost'] = $this->calculateMaterialCostRecursive($cost->product_id);
logger("Chi phí nguyên vật liệu cho sản phẩm {$data['product_id']}: " . $data['material_cost']);

        $data['total_cost'] = $data['material_cost'] + array_sum(array_map('floatval', [
            $data['overhead_cost'],
            $data['inventory_cost'],
            $data['transportation_cost'],
            $data['wastage_cost'],
            $data['depreciation_cost'],
            $data['service_outsourcing_cost'],
            $data['other_costs']
        ]));

        $cost->update($data);

        ProductCostHistory::create([
            'product_id' => $cost->product_id,
            'year' => Carbon::now()->year,
            'old_total_cost' => $oldTotal,
            'total_cost' => $data['total_cost'],
            'reason' => 'Cập nhật theo chi phí mới'
        ]);

        return response()->json(['message' => 'Cập nhật chi phí thành công', 'data' => $cost]);
    }

    public function destroy($id)
    {
        ProductCost::destroy($id);
        return response()->json(['message' => 'Chi phí sản phẩm đã xoá']);
    }

    protected function calculateMaterialCostRecursive($outputId, $outputType = 'products', $multiplier = 1)
    {
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
                    $multiplier * $item->quantity_input
                );
            }
        }

        return $total;
    }
}
