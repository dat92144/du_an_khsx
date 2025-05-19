<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCostHistory;

class ProductCostHistoryController extends Controller
{
    public function index()
    {
        return ProductCostHistory::with('product')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'year' => 'required|digits:4|integer',
            'total_cost' => 'required|numeric',
            'old_total_cost' => 'nullable|numeric',
            'reason' => 'nullable|string'
        ]);

        $history = ProductCostHistory::updateOrCreate(
            ['product_id' => $validated['product_id'], 'year' => $validated['year']],
            $validated
        );

        return response()->json([
            'message' => 'Lưu lịch sử chi phí thành công',
            'data' => $history
        ]);
    }
    public function destroy($id)
{
    ProductCostHistory::destroy($id);
    return response()->json(['message' => 'Đã xoá lịch sử chi phí']);
}

}
