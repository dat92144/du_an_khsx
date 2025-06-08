<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GanttController extends Controller
{
    public function forOrders()
    {
        $orders = DB::table('orders')->get();
        $plans = DB::table('production_plans')->get();

        $tasks = [];

        foreach ($orders as $order) {
            $orderPlans = $plans->where('order_id', $order->id);

            $totalQtyOrder = $orderPlans->sum('total_quantity');
            $doneQtyOrder = $orderPlans->sum('quantity_done');
            $progress = $totalQtyOrder > 0 ? $doneQtyOrder / $totalQtyOrder : 0;

            $tasks[] = [
                'id' => $order->id,
                'text' => 'Đơn hàng ' . $order->id,
                'start_date' => Carbon::parse($order->order_date)->format('Y-m-d H:i:s'),
                'duration' => Carbon::parse($order->order_date)->diffInDays($order->delivery_date),
                'progress' => $progress,
                'open' => true
            ];
        }

        return response()->json([
            'data' => $tasks,
            'links' => []
        ]);
    }

    public function productWithLots()
    {
        $tasks = [];

        $allPlans = DB::table('production_plans')
            ->whereNotNull('product_id')
            ->orWhereNotNull('semi_finished_product_id')
            ->get();

        $grouped = $allPlans->groupBy('order_id');

        foreach ($grouped as $orderId => $plans) {
            $firstPlan = $plans->first();
            $start = $plans->min('start_time');
            $end = $plans->max('end_time');

            $itemId = $firstPlan->product_id ?? $firstPlan->semi_finished_product_id;
            $type = $firstPlan->product_id ? 'product' : 'semi';

            $totalQtyOrder = $plans->sum('total_quantity');
            $doneQtyOrder = $plans->sum('quantity_done');

            $tasks[] = [
                'id' => "order-$orderId",
                'text' => ($type === 'product' ? "ĐH-$orderId Sản phẩm " : "ĐH-$orderId BTP ") . $itemId,
                'start_date' => $start,
                'duration' => Carbon::parse($start)->diffInMinutes($end) / 60,
                'progress' => $totalQtyOrder > 0 ? $doneQtyOrder / $totalQtyOrder : 0,
                'open' => true
            ];

            $lots = $plans->groupBy('lot_number');
            foreach ($lots as $lot => $lotPlans) {
                $lotStart = $lotPlans->min('start_time');
                $lotEnd = $lotPlans->max('end_time');
                $totalQty = $lotPlans->sum('total_quantity');
                $doneQty = $lotPlans->sum('quantity_done');

                $tasks[] = [
                    'id' => "lot-$orderId-$lot-$type-$itemId",
                    'text' => "Lô $lot",
                    'start_date' => $lotStart,
                    'duration' => Carbon::parse($lotStart)->diffInMinutes($lotEnd) / 60,
                    'parent' => "order-$orderId",
                    'progress' => $totalQty > 0 ? $doneQty / $totalQty : 0,
                    'open' => false
                ];
            }
        }

        return response()->json([
            'data' => $tasks,
            'links' => []
        ]);
    }

    public function getLotDetail(Request $request)
    {
        $productId = $request->query('product_id');
        $lot = $request->query('lot');
        $type = $request->query('type', 'product');

        $plans = DB::table('production_plans')
            ->where('lot_number', $lot);

        if ($type === 'product') {
            $plans = $plans->where('product_id', $productId);
        } else {
            $plans = $plans->where('semi_finished_product_id', $productId);
        }

        $plans = $plans->get();

        $tasks = [];

        foreach ($plans as $plan) {
            $tasks[] = [
                'id' => $plan->id,
                'text' => $plan->process_id . ' (' . $plan->machine_id . ')',
                'start_date' => $plan->start_time,
                'duration' => Carbon::parse($plan->start_time)->diffInMinutes($plan->end_time) / 60,
                'progress' => $plan->total_quantity > 0 ? $plan->quantity_done / $plan->total_quantity : 0,
                'open' => true
            ];
        }

        return response()->json([
            'data' => $tasks,
            'links' => []
        ]);
    }

    public function getRealtimeMachineStatus()
    {
        $runningPlans = DB::table('production_plans')
            ->where('status', 'working')
            ->get();

        $data = [];

        foreach ($runningPlans as $plan) {
            $data[] = [
                'machine_id' => $plan->machine_id,
                'plan_id' => $plan->id,
                'product_id' => $plan->product_id,
                'current_product' => $plan->product_id,
                'process_id' => $plan->process_id,
                'process_name' => $plan->process_id,
                'lot_number' => $plan->lot_number,
                'quantity_total' => $plan->total_quantity,
                'quantity_done' => $plan->quantity_done,
                'status' => 'working',
                'start_time' => $plan->start_time,
                'end_time' => $plan->end_time,
                'timestamp' => now()->toISOString()
            ];
        }

        return response()->json($data);
    }

    public function getMachineGantt()
    {
        $plans = DB::table('production_plans')->get();
        $tasks = [];

        $machines = $plans->groupBy('machine_id');

        foreach ($machines as $machineId => $machinePlans) {
            $machineStart = $machinePlans->min('start_time');
            $machineEnd = $machinePlans->max('end_time');

            $tasks[] = [
                'id' => 'machine-' . $machineId,
                'text' => 'Máy ' . $machineId,
                'start_date' => $machineStart,
                'duration' => max(1, Carbon::parse($machineStart)->diffInMinutes($machineEnd) / 60),
                'progress' => 0,
                'open' => true
            ];

            $byOrder = $machinePlans->groupBy('order_id');

            foreach ($byOrder as $orderId => $orderPlans) {
                $orderStart = $orderPlans->min('start_time');
                $orderEnd = $orderPlans->max('end_time');

                $totalQtyOrder = $orderPlans->sum('total_quantity');
                $doneQtyOrder = $orderPlans->sum('quantity_done');

                $tasks[] = [
                    'id' => 'order-' . $machineId . '-' . $orderId,
                    'text' => "ĐH $orderId",
                    'start_date' => $orderStart,
                    'duration' => max(1, Carbon::parse($orderStart)->diffInMinutes($orderEnd) / 60),
                    'parent' => 'machine-' . $machineId,
                    'progress' => $totalQtyOrder > 0 ? $doneQtyOrder / $totalQtyOrder : 0,
                    'open' => true
                ];

                foreach ($orderPlans as $plan) {
                    $tasks[] = [
                        'id' => 'plan-' . $plan->id,
                        'text' => "Lệnh {$plan->id} ({$plan->process_id} - Lô {$plan->lot_number})",
                        'start_date' => $plan->start_time,
                        'duration' => max(1, Carbon::parse($plan->start_time)->diffInMinutes($plan->end_time) / 60),
                        'parent' => 'order-' . $machineId . '-' . $orderId,
                        'progress' => $plan->total_quantity > 0 ? $plan->quantity_done / $plan->total_quantity : 0
                    ];
                }
            }
        }

        return response()->json([
            'data' => $tasks,
            'links' => []
        ]);
    }
}
