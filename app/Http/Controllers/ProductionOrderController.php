<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
class ProductionOrderController extends Controller {
    public function index()
    {
        $orders = ProductionOrder::with(['product', 'order.customer'])->get();
    
        $data = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_id' => $order->order_id,
                'product_name' => optional($order->product)->name ?? 'Không có sản phẩm',
                'customer_name' => optional(optional($order->order)->customer)->name ?? 'Không có khách hàng',
                'status' => $order->producing_status,
                'start_date' => $order->order_date,
                'end_date' => $order->delivery_date,
            ];
        });
    
        return response()->json($data);
    }
    
    public function show($id) {
        $productionOrder = ProductionOrder::find($id);
        if (!$productionOrder) return response()->json(['message' => 'Production Order not found'], 404);
        return response()->json($productionOrder, 200);
    }
    
    public function store(Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|string|exists:products,id',
            'order_quantity' => 'required|integer|min:1',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date', // Cho phép nhập hoặc để trống
            'bom_id' => 'required|string|exists:boms,id',
            'producing_status' => 'required|string'
        ]);
    
        $productSpec = DB::table('specs')->where('product_id', $validated['product_id'])->first();
    
        if (!$productSpec) {
            return response()->json(['message' => 'Không tìm thấy thông số kỹ thuật sản phẩm!'], 400);
        }
    
        $leadTime = $productSpec->lead_time ?? 0; // Đơn vị: ngày
        $calculatedDeliveryDate = Carbon::parse($validated['order_date'])->addDays($leadTime);
    
        if (!isset($validated['delivery_date']) || Carbon::parse($validated['delivery_date'])->lt($calculatedDeliveryDate)) {
            $validated['delivery_date'] = $calculatedDeliveryDate;
        }
    
        $latest = DB::table('production_orders')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'PRODORDER' . str_pad(intval(substr($latest->id, 10)) + 1, 3, '0', STR_PAD_LEFT) : 'PRODORDER001';
    
        $validated['id'] = $newId;
    
        $productionOrder = ProductionOrder::updateOrCreate(
            ['product_id' => $validated['product_id'], 'order_date' => $validated['order_date']],
            $validated
        );
    
        return response()->json([
            'message' => 'Kế hoạch sản xuất đã được cập nhật!',
            'production_order' => $productionOrder
        ], 201);
    }        

    public function update(Request $request, $id) {
        $productionOrder = ProductionOrder::find($id);
        if (!$productionOrder) return response()->json(['message' => 'Production Order not found'], 404);

        $validated = $request->validate([
            'product_id' => 'exists:products,id',
            'order_quantity' => 'integer|min:1',
            'order_date' => 'date',
            'delivery_date' => 'date|after_or_equal:order_date',
            'bom_id' => 'exists:boms,id',
            'producing_status' => 'string|in:planned,producing,completed',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time'
        ]);

        $productionOrder->update($validated);
        return response()->json($productionOrder, 200);
    }

    public function destroy($id) {
        $productionOrder = ProductionOrder::find($id);
        if (!$productionOrder) return response()->json(['message' => 'Production Order not found'], 404);

        $productionOrder->delete();
        return response()->json(['message' => 'Production Order deleted successfully'], 200);
    }
    public function approveProductionOrder(Request $request, $productionOrderId)
    {
        $po = ProductionOrder::find($productionOrderId);

        if (!$po) {
            return response()->json(['message' => 'Không tìm thấy lệnh sản xuất'], 404);
        }
        $po->producing_status = 'approved';
        $po->save();
        Artisan::call('app:generate-production-plan');

        return response()->json(['message' => '✅ Đã duyệt lệnh sản xuất và bắt đầu xử lý kế hoạch']);
    }

}
