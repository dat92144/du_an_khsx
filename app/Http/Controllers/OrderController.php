<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\OrderDetails;

class OrderController extends Controller {
    public function index() {
        $orders = Order::with('customer')->get();
    
        $orders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'customer_name' => $order->customer ? $order->customer->name : 'Không có khách hàng',
                'order_date' => $order->order_date,
                'delivery_date' => $order->delivery_date,
                'status' => $order->status
            ];
        });
    
        return response()->json($orders, 200);
    }    

    public function show($id) {
        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);
        return response()->json($order, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'customer_id' => 'required|string|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date',
            'status' => 'required|string'
        ]);
    
        $latest = DB::table('orders')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'o' . str_pad(intval(substr($latest->id, 3)) + 1, 3, '0', STR_PAD_LEFT) : 'o001';
    
        $validated['id'] = $newId;
        $order = Order::create($validated);
    
        return response()->json($order, 201);
    }    

    public function update(Request $request, $id) {
        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $validated = $request->validate([
            'customer_id' => 'string|exists:customers,id',
            'order_date' => 'date',
            'delivery_date' => 'date',
            'status' => 'string|max:50'
        ]);

        $order->update($validated);
        return response()->json($order, 200);
    }

    public function destroy($id) {
        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $order->delete();
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }

    public function getOrderItems($orderId) {
        $orderDetails = OrderDetails::where('order_id', $orderId)
            ->with(['product:id,name', 'unit:id,name']) // Chỉ lấy các trường cần thiết
            ->get();
    
        $result = $orderDetails->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => optional($item->product)->name ?? 'Không có sản phẩm',
                'quantity' => $item->quantity_product ?? 0,
                'unit_name' => optional($item->unit)->name ?? 'Không có đơn vị'
            ];
        });
    
        return response()->json($result, 200);
    }    
}
