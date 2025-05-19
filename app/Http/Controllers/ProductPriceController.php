<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\DB;

class ProductPriceController extends Controller
{
    public function index()
    {
        return ProductPrice::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'year' => 'required|digits:4|integer',
            'total_cost' => 'nullable|numeric',
            'expected_profit_percent' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        if (empty($validated['total_cost'])) {
            $validated['total_cost'] = DB::table('product_costs')
                ->where('product_id', $validated['product_id'])
                ->orderByDesc('updated_at')
                ->value('total_cost') ?? 0;
        }

        $validated['sell_price'] = $validated['total_cost'] * (1 + $validated['expected_profit_percent'] / 100);

        $price = ProductPrice::create($validated);
        return response()->json(['message' => 'Tính giá bán thành công', 'data' => $price]);
    }

    public function update(Request $request, $id)
    {
        $price = ProductPrice::findOrFail($id);
        $validated = $request->validate([
            'total_cost' => 'required|numeric',
            'expected_profit_percent' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);
        $validated['sell_price'] = $validated['total_cost'] * (1 + $validated['expected_profit_percent'] / 100);
        $price->update($validated);

        return response()->json(['message' => 'Cập nhật giá bán thành công', 'data' => $price]);
    }

    public function destroy($id)
    {
        ProductPrice::destroy($id);
        return response()->json(['message' => 'Đã xoá giá bán sản phẩm']);
    }

    // 🆕 Hàm gọi nội bộ để tự động cập nhật giá bán khi chi phí thay đổi
    public static function updateOrCreateLatestPrice($productId)
    {
        $totalCost = DB::table('product_costs')
            ->where('product_id', $productId)
            ->orderByDesc('updated_at')
            ->value('total_cost');

        if ($totalCost === null) {
            return null; // Không có dữ liệu chi phí
        }

        // Lấy % lợi nhuận gần nhất hoặc mặc định 10%
        $latestPrice = DB::table('product_prices')
            ->where('product_id', $productId)
            ->orderByDesc('created_at')
            ->first();

        $profitPercent = $latestPrice->expected_profit_percent ?? 10;

        // Tính lại giá bán
        $sellPrice = $totalCost * (1 + $profitPercent / 100);

        // Tạo mới bản ghi giá bán (năm hiện tại)
        return ProductPrice::create([
            'product_id' => $productId,
            'year' => now()->year,
            'total_cost' => $totalCost,
            'expected_profit_percent' => $profitPercent,
            'sell_price' => $sellPrice,
            'is_active' => true,
        ]);
    }
}
