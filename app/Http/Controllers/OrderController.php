<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductionOrder;
use App\Events\ProductionOrderCreated;
use App\Models\BomItem;
use App\Models\Inventory;
use App\Models\Spec;
use App\Models\SupplierPrice;
use App\Models\MachineSchedule;
use App\Models\PurchaseRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $details = $request->details;
        $orderId = $request->id;

        // Tính ngày giao hàng chính xác
        $deliveryDate = $this->calculatePreciseDeliveryDate($details);

        // Tạo đơn hàng
        $order = Order::create([
            'id' => $orderId,
            'customer_id' => $request->customer_id,
            'order_date' => $request->order_date,
            'delivery_date' => $deliveryDate,
            'status' => 'pending'
        ]);

        foreach ($details as $item) {
            $productId = $item['product_id'];
            $productType = $item['product_type'];
            $semiId = $productType === 'semi_finished_product' ? $productId : null;
            $prodId = $productType === 'product' ? $productId : null;

            // Lưu chi tiết đơn hàng
            OrderDetail::create([
                'id' => $item['id'],
                'order_id' => $orderId,
                'product_type' => $productType,
                'product_id' => $prodId,
                'semi_finished_product_id' => $semiId,
                'quantity_product' => $item['quantity_product'],
                'unit_id' => $item['unit_id']
            ]);

        }
        return response()->json(['message' => 'Đã tạo đơn hàng và lập kế hoạch sản xuất']);
    }
    private function calculatePreciseDeliveryDate($orderDetails)
    {
        $latestMaterialDelivery = Carbon::now();
        $latestProductionEnd = Carbon::now();

        foreach ($orderDetails as $item) {
            $productId = $item['product_id'];
            $productType = $item['product_type'];
            $quantity = $item['quantity_product'];

            $unit_order = $item['unit_id'] ?? null;
            $unit = DB::table('units')->where('id', $unit_order)->first();
            if ($unit->name === 'Bao') {
                //$this->info("⚖️ Quy đổi $quantity bao → " . ($quantity * 0.05) . " tấn");
                $quantity *= 0.05;
            }

            $bom = Bom::where($productType === 'product' ? 'product_id' : 'semi_finished_product_id', $productId)->first();
            if (!$bom) continue;

            // 📌 1. Tính ngày giao nguyên vật liệu trễ nhất
            $bomItems = BomItem::where('bom_id', $bom->id)->get();

            foreach ($bomItems as $bomItem) {
                $materialId = $bomItem->input_material_id;
                $type = $bomItem->input_material_type;
                $requiredQty = $bomItem->quantity_input * $quantity;

                $stock = Inventory::where('item_id', $materialId)
                    ->where('item_type', $type)
                    ->value('quantity') ?? 0;

                $expectedDelivery = PurchaseRequests::where('material_id', $materialId)
                    ->where('type', $type)
                    ->where('status', 'pending')
                    ->orderByDesc('expected_delivery_date')
                    ->value('expected_delivery_date');

                if (!$expectedDelivery) {
                    $supplier = SupplierPrice::where('material_id', $materialId)
                        ->orderBy('delivery_time', 'asc')->first();
                    $expectedDelivery = $supplier
                        ? Carbon::now()->addDays($supplier->delivery_time ?? 3)
                        : Carbon::now()->addDays(5);
                }

                $latestMaterialDelivery = Carbon::parse($expectedDelivery)->gt($latestMaterialDelivery)
                    ? Carbon::parse($expectedDelivery)
                    : $latestMaterialDelivery;
            }

            // 📌 2. Tính ngày kết thúc kế hoạch sản xuất trễ nhất trên tất cả các máy
            $specs = Spec::where($productType === 'product' ? 'product_id' : 'semi_finished_product_id', $productId)->get();

            foreach ($specs as $step) {
                $lastUsed = MachineSchedule::where('machine_id', $step->machine_id)
                    ->orderByDesc('end_time')
                    ->value('end_time');

                if ($lastUsed) {
                    $latestProductionEnd = Carbon::parse($lastUsed)->gt($latestProductionEnd)
                        ? Carbon::parse($lastUsed)
                        : $latestProductionEnd;
                }
            }

            // 📌 3. Cộng thêm thời gian sản xuất dự kiến theo lô
            $lotSize = $specs->first()?->lot_size ?? 1;
            $cycleTime = $specs->sum('cycle_time');
            $numLots = ceil($quantity / max(1, $lotSize));
            $totalTimeMinutes = $cycleTime * $quantity;

            $estimatedProductionTime = ceil($totalTimeMinutes / (8 * 60)); // theo ngày làm việc
            $latestProductionEnd->addDays($estimatedProductionTime);
        }

        // 📌 4. Ngày giao hàng là muộn nhất giữa nguyên vật liệu và sản xuất
        return $latestMaterialDelivery->gt($latestProductionEnd) ? $latestMaterialDelivery : $latestProductionEnd;
    }

    public function estimateDelivery(Request $request)
    {
        $deliveryDate = $this->calculatePreciseDeliveryDate($request->details);
        return response()->json(['delivery_date' => $deliveryDate]);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // ✅ Tính lại ngày giao hàng dựa trên chi tiết
        $deliveryDate = $this->calculatePreciseDeliveryDate($request->details);
        $order->update([
            'customer_id' => $request->customer_id,
            'order_date' => $request->order_date,
            'delivery_date' => $deliveryDate
        ]);

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

        return response()->json([
            'message' => 'Đã cập nhật đơn hàng',
            'delivery_date' => $deliveryDate
        ]);
    }


    public function destroy($id)
    {
        OrderDetail::where('order_id', $id)->delete();
        Order::where('id', $id)->delete();

        return response()->json(['message' => 'Đã xoá đơn hàng']);
    }

    // public function produce($id)
    // {
    //     $order = Order::with('details')->findOrFail($id);

    //     foreach ($order->details as $detail) {
    //         $productId = $detail->product_id;
    //         $semiId = $detail->semi_finished_product_id;

    //         // Kiểm tra loại sản phẩm và tìm BOM chính xác
    //         if (!empty($productId)) {
    //             $bom = Bom::where('product_id', $productId)->first();
    //         } elseif (!empty($semiId)) {
    //             $bom = Bom::where('semi_finished_product_id', $semiId)->first();
    //         } else {
    //             return response()->json([
    //                 'message' => 'Sản phẩm không có ID hợp lệ.'
    //             ], 400);
    //         }

    //         if (!$bom) {
    //             return response()->json([
    //                 'message' => 'Không tìm thấy BOM cho sản phẩm: ' . ($productId ?? $semiId)
    //             ], 400);
    //         }

    //         $po = ProductionOrder::create([
    //             'id' => $this->generateProductionOrderId(),
    //             'product_id' => $productId,
    //             'semi_finished_product_id' => $semiId,
    //             'order_id' => $order->id,
    //             'order_quantity' => $detail->quantity_product,
    //             'order_date' => $order->order_date,
    //             'delivery_date' => $order->delivery_date,
    //             'bom_id' => $bom->id,
    //             'producing_status' => 'pending',
    //         ]);

    //         event(new ProductionOrderCreated($po));
    //     }

    //     $order->update(['status' => 'approved']);
    //     return response()->json(['message' => 'Đã tạo kế hoạch sản xuất từ đơn hàng']);
    // }

    public function produce($id)
    {
        $order = Order::with('details')->findOrFail($id);

        foreach ($order->details as $detail) {
            $productId = $detail->product_id;
            $semiId = $detail->semi_finished_product_id;
            $productType = $detail->product_type;
            $quantity = $detail->quantity_product;

            $targetId = $productId ?? $semiId;
            $bom = Bom::where($productType === 'product' ? 'product_id' : 'semi_finished_product_id', $targetId)->first();
            if (!$bom) continue;

            $stock = Inventory::where('item_id', $targetId)
                ->where('item_type', $productType)
                ->value('quantity') ?? 0;

            $planned = DB::table('production_orders')
                ->where('producing_status', '!=', 'completed')
                ->where($productType === 'product' ? 'product_id' : 'semi_finished_product_id', $targetId)
                ->sum('order_quantity');

            $gap = max(0, $quantity - $stock - $planned);

            // if ($gap <= 0) {
            //     continue; // Đủ hàng hoặc đã lên kế hoạch rồi
            // }

            $po = ProductionOrder::create([
                'id' => $this->generateProductionOrderId(),
                'product_id' => $productId,
                'semi_finished_product_id' => $semiId,
                'order_id' => $order->id,
                'order_quantity' => $gap,
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
