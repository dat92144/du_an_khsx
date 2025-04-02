<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\{
    Order, OrderDetails, Product, RoutingBom, ProductionOrder,
    ProductionPlan, MachineSchedule, InventoryMaterial, ProductionHistory, Spec, BomItem
};
use Illuminate\Support\Str;

class ProductionPlanningController extends Controller
{
    public function autoPlan()
    {
        $orders = Order::where('status', 'confirmed')
            ->orderBy('order_date')
            ->with('orderDetails')
            ->get();

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $product = Product::find($detail->product_id);
                if (!$product) continue;

                $productType = $detail->product_type ?? 'product';
                $spec = Spec::where('product_id', $product->id)->first();
                $lotSize = $spec->lot_size ?? 1;

                $routing = RoutingBom::where('product_id', $product->id)
                    ->orderBy('step_order')
                    ->get();
                if ($routing->isEmpty()) continue;

                $bomId = DB::table('boms')->where('product_id', $product->id)->value('id');
                if (!$bomId) continue;

                $productionOrderId = $this->generateId('production_orders', 'PRODOR');
                ProductionOrder::create([
                    'id' => $productionOrderId,
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'order_quantity' => $detail->quantity_product,
                    'order_date' => $order->order_date,
                    'delivery_date' => $order->delivery_date,
                    'bom_id' => $bomId,
                    'producing_status' => 'planned',
                ]);

                $totalQty = $detail->quantity_product;
                $numLots = ceil($totalQty / $lotSize);
                $startDate = Carbon::now();

                for ($lot = 1; $lot <= $numLots; $lot++) {
                    $lotQty = ($lot == $numLots) ? $totalQty - $lotSize * ($numLots - 1) : $lotSize;

                    foreach ($routing as $step) {
                        $machineId = $step->machine_id;
                        $cycleTime = $step->cycle_time ?? 0;
                        $durationMinutes = ceil($cycleTime * $lotQty);

                        $remaining = $this->getRemainingCapacity($machineId, $startDate);
                        if ($remaining < $lotQty) {
                            $startDate->addDay();
                            $lot--;
                            continue 2;
                        }

                        $startTime = $this->getAvailableTime($machineId, $durationMinutes, $startDate);
                        $endTime = (clone $startTime)->addMinutes($durationMinutes);

                        $machineScheduleId = $this->generateId('machine_schedules', 'MS');
                        MachineSchedule::create([
                            'id' => $machineScheduleId,
                            'machine_id' => $machineId,
                            'production_order_id' => $productionOrderId,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'status' => 'scheduled'
                        ]);

                        $planId = $this->generateId('production_plans', 'PLAN', 3, 'plan_id');
                        ProductionPlan::create([
                            'plan_id' => $planId,
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'lot_number' => $lot,
                            'lot_size' => $lotQty,
                            'total_quantity' => $totalQty,
                            'machine_id' => $machineId,
                            'process_id' => $step->process_id,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'delivery_date' => $order->delivery_date,
                            'status' => 'planned'
                        ]);

                        $startDate = $endTime;
                    }

                    $historyId = $this->generateId('production_histories', 'HIST');
                    ProductionHistory::create([
                        'id' => $historyId,
                        'production_order_id' => $productionOrderId,
                        'product_id' => $product->id,
                        'completed_quantity' => $lotQty,
                        'date' => now(),
                    ]);

                    // Cập nhật kho theo loại sản phẩm
                    if ($productType === 'semi_finished_product') {
                        DB::table('inventory_semi_products')->updateOrInsert(
                            ['semi_product_id' => $product->id],
                            ['quantity' => DB::raw("quantity + $lotQty")]
                        );
                    } else {
                        DB::table('inventory_products')->updateOrInsert(
                            ['product_id' => $product->id],
                            ['quantity' => DB::raw("quantity + $lotQty")]
                        );
                    }

                    $bomItems = BomItem::where('product_id', $product->id)->get();
                    foreach ($bomItems as $item) {
                        $used = $item->quantity_input * $lotQty;
                        DB::table('inventory_materials')
                            ->where('material_id', $item->input_material_id)
                            ->decrement('quantity', $used);
                    }
                }

                $order->update(['status' => 'planned']);
            }
        }

        return response()->json(['message' => 'Đã lập kế hoạch sản xuất tự động.']);
    }

    public function getPlans()
    {
        $plans = ProductionPlan::with(['product', 'machine', 'process'])
            ->orderBy('start_time')
            ->get();

        return response()->json($plans);
    }

    private function getRemainingCapacity($machineId, $date)
    {
        $capacity = DB::table('machine_capacity')
            ->where('machine_id', $machineId)
            ->value('max_output_per_day') ?? 999999;

        $scheduled = MachineSchedule::where('machine_id', $machineId)
            ->whereDate('start_time', $date->toDateString())
            ->join('production_orders', 'machine_schedules.production_order_id', '=', 'production_orders.id')
            ->sum('production_orders.order_quantity');

        return $capacity - $scheduled;
    }

    private function getAvailableTime($machineId, $lotDurationMinutes, $date)
    {
        $latest = MachineSchedule::where('machine_id', $machineId)
            ->whereDate('start_time', $date->toDateString())
            ->orderBy('end_time', 'desc')
            ->first();

        if ($latest) {
            return Carbon::parse($latest->end_time);
        }

        return $date->copy()->setTime(8, 0);
    }

    private function generateId($table, $prefix, $length = 3, $idColumn = 'id')
    {
        $latest = DB::table($table)
            ->where($idColumn, 'like', $prefix . '%')
            ->orderBy($idColumn, 'desc')
            ->value($idColumn);

        if (!$latest) {
            return $prefix . str_pad(1, $length, '0', STR_PAD_LEFT);
        }

        $number = intval(substr($latest, strlen($prefix))) + 1;
        return $prefix . str_pad($number, $length, '0', STR_PAD_LEFT);
    }
    public function getPlansByOrder($orderId)
    {
        $plans = ProductionPlan::where('order_id', $orderId)
            ->with(['product', 'process', 'machine']) // nếu có quan hệ
            ->orderBy('lot_number')
            ->get();

        return response()->json($plans, 200);
    }
}
