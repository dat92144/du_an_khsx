<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductionOrder;
class OrderController extends Controller
{
    public function index()
    {
        return Order::with('details')->get();
    }

    public function store(Request $request)
    {
        $order = Order::create([
            'id' => $request->id,
            'customer_id' => $request->customer_id,
            'order_date' => $request->order_date,
            'delivery_date' => $request->delivery_date,
            'status' => 'pending'
        ]);

        foreach ($request->details as $item) {
            OrderDetail::create([
                'id' => $item['id'],
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_type' => 'product',
                'quantity_product' => $item['quantity_product'],
                'unit_id' => $item['unit_id']
            ]);
        }

        return response()->json(['message' => 'Đã tạo đơn hàng thành công']);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->only(['customer_id', 'order_date', 'delivery_date']));

        OrderDetail::where('order_id', $id)->delete();

        foreach ($request->details as $item) {
            OrderDetail::create([
                'id' => $item['id'],
                'order_id' => $id,
                'product_id' => $item['product_id'],
                'product_type' => $item['product_type'],
                'quantity_product' => $item['quantity_product'],
                'unit_id' => $item['unit_id'],
            ]);
        }
    }

    public function destroy($id)
    {
        Order::where('id', $id)->delete();
        OrderDetail::where('order_id', $id)->delete();

        return response()->json(['message' => 'Đã xoá đơn hàng']);
    }

    public function produce($id)
    {
        $order = Order::with('details')->findOrFail($id);
        foreach ($order->details as $detail) {
            $bom = Bom::where('product_id', $detail->product_id)->first();
            if(!$bom){
                return response()->json(['message'=>'không tìm thấy bom phù hợp']);
            }
            ProductionOrder::create([
                'order_id' => $order->id,
                'product_id' => $detail->product_id,
                'order_quantity' => $detail->quantity_product,
                'order_date' => $order->order_date,
                'delivery_date' =>$order->delivery_date,
                'bom_id' =>$bom->id,
                'producing_status' => 'pending',
            ]);
        }

        $order->update(['status' => 'approved']);

        return response()->json(['message' => 'Đã thêm vào kế hoạch sản xuất']);
    }
}
