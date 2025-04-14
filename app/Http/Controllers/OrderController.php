<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductionOrder;
use App\Events\ProductionOrderCreated;
class OrderController extends Controller
{
    private function generateProductionOrderId()
    {
        $latest = \App\Models\ProductionOrder::orderBy('id', 'desc')->first();
        if (!$latest || !preg_match('/^PO\d+$/', $latest->id)) {
            return 'PO001';
        }

        $number = (int) substr($latest->id, 2);
        return 'PO' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

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
            $productType = $item['product_type'];
            $productId = $item['product_id'];

            OrderDetail::create([
                'id' => $item['id'],
                'order_id' => $order->id,
                'product_type' => $productType,
                'product_id' => $productType === 'product' ? $productId : null,
                'semi_finished_product_id' => $productType === 'semi_finished_product' ? $productId : null,
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
            $productType = $item['product_type'];
            $productId = $item['product_id'];

            OrderDetail::create([
                'id' => $item['id'],
                'order_id' => $id,
                'product_type' => $productType,
                'product_id' => $productType === 'product' ? $productId : null,
                'semi_finished_product_id' => $productType === 'semi_finished_product' ? $productId : null,
                'quantity_product' => $item['quantity_product'],
                'unit_id' => $item['unit_id']
            ]);
        }


        return response()->json(['message' => 'Đã cập nhật đơn hàng']);
    }

    public function destroy($id)
    {
        OrderDetail::where('order_id', $id)->delete();
        Order::where('id', $id)->delete();

        return response()->json(['message' => 'Đã xoá đơn hàng']);
    }

    public function produce($id)
    {
        $order = Order::with('details')->findOrFail($id);

        foreach ($order->details as $detail) {
            $productId = $detail->product_id;
            $semiId = $detail->semi_finished_product_id;

            // Kiểm tra loại sản phẩm và tìm BOM chính xác
            if (!empty($productId)) {
                $bom = Bom::where('product_id', $productId)->first();
            } elseif (!empty($semiId)) {
                $bom = Bom::where('semi_finished_product_id', $semiId)->first();
            } else {
                return response()->json([
                    'message' => 'Sản phẩm không có ID hợp lệ.'
                ], 400);
            }

            if (!$bom) {
                return response()->json([
                    'message' => 'Không tìm thấy BOM cho sản phẩm: ' . ($productId ?? $semiId)
                ], 400);
            }

            $po = ProductionOrder::create([
                'id' => $this->generateProductionOrderId(),
                'product_id' => $productId,
                'semi_finished_product_id' => $semiId,
                'order_id' => $order->id,
                'order_quantity' => $detail->quantity_product,
                'order_date' => $order->order_date,
                'delivery_date' => $order->delivery_date,
                'bom_id' => $bom->id,
                'producing_status' => 'pending',
            ]);

            event(new ProductionOrderCreated($po));
        }

        $order->update(['status' => 'approved']);
        return response()->json(['message' => 'Đã tạo kế hoạch sản xuất từ đơn hàng']);
    }

}
