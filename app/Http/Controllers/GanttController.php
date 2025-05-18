<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionPlan;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;

class GanttController extends Controller
{


    public function forOrders()
    {
        $orders = DB::table('orders')->get();
        $plans = DB::table('production_plans')->get();

        $tasks = [];

        foreach ($orders as $order) {
            $orderPlans = $plans->where('order_id', $order->id);
            $total = $orderPlans->count();
            $finished = $orderPlans->where('status', 'finished')->count();

            $progress = $total > 0 ? $finished / $total : 0;

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

    // Lấy tất cả kế hoạch đã có product hoặc bán thành phẩm
    $allPlans = DB::table('production_plans')
        ->whereNotNull('product_id')
        ->orWhereNotNull('semi_finished_product_id')
        ->get();

    // Gom theo production_order_id
    $grouped = $allPlans->groupBy('order_id');

    foreach ($grouped as $orderId => $plans) {
        $firstPlan = $plans->first();
        $start = $plans->min('start_time');
        $end = $plans->max('end_time');

        $itemId = $firstPlan->product_id ?? $firstPlan->semi_finished_product_id;
        $type = $firstPlan->product_id ? 'product' : 'semi';

        $tasks[] = [
            'id' => "order-$orderId",
            'text' => ($type === 'product' ? "ĐH-$orderId Sản phẩm " : "ĐH-$orderId BTP ") . $itemId,
            'start_date' => $start,
            'duration' => max(1, Carbon::parse($start)->diffInMinutes($end) / 60),
            'progress' => 0,
            'open' => true
        ];

        $lots = $plans->groupBy('lot_number');
        foreach ($lots as $lot => $lotPlans) {
            $lotStart = $lotPlans->min('start_time');
            $lotEnd = $lotPlans->max('end_time');

            $tasks[] = [
                'id' => "lot-$orderId-$lot",
                'text' => "Lô $lot",
                'start_date' => $lotStart,
                'duration' => max(1, Carbon::parse($lotStart)->diffInMinutes($lotEnd) / 60),
                'parent' => "order-$orderId",
                'progress' => 0,
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
    $type = $request->query('type', 'product'); // default = product

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
            'id' => $plan->plan_id,
            'text' => $plan->process_id . ' (' . $plan->machine_id . ')',
            'start_date' => $plan->start_time,
            'duration' => max(1, Carbon::parse($plan->end_time)->diffInMinutes($plan->start_time) / 60),
            'progress' => $plan->status === 'finished' ? 1 : 0,
            'open' => true
        ];
    }

    return response()->json([
        'data' => $tasks,
        'links' => []
    ]);
}

    public function getMachineGantt()
{
    $plans = DB::table('production_plans')->get();
    $tasks = [];

    $machines = $plans->groupBy('machine_id');

    foreach ($machines as $machineId => $machinePlans) {
        $machineStart = $machinePlans->min('start_time');
        $machineEnd = $machinePlans->max('end_time');

        // Task cha: máy
        $tasks[] = [
            'id' => 'machine-' . $machineId,
            'text' => 'Máy ' . $machineId,
            'start_date' => $machineStart,
            'duration' => max(1, Carbon::parse($machineStart)->diffInMinutes($machineEnd) / 60),
            'progress' => 0,
            'open' => true
        ];

        // Gom các kế hoạch theo production_order_id
        $byOrder = $machinePlans->groupBy('order_id');

        foreach ($byOrder as $orderId => $orderPlans) {
            $orderStart = $orderPlans->min('start_time');
            $orderEnd = $orderPlans->max('end_time');

            // Task trung gian: đơn hàng
            $tasks[] = [
                'id' => 'order-' . $machineId . '-' . $orderId,
                'text' => "ĐH $orderId",
                'start_date' => $orderStart,
                'duration' => max(1, Carbon::parse($orderStart)->diffInMinutes($orderEnd) / 60),
                'parent' => 'machine-' . $machineId,
                'progress' => 0,
                'open' => true
            ];

            foreach ($orderPlans as $plan) {
                $tasks[] = [
                    'id' => 'plan-' . $plan->plan_id,
                    'text' => "Lệnh {$plan->plan_id} ({$plan->process_id} - Lô {$plan->lot_number})",
                    'start_date' => $plan->start_time,
                    'duration' => max(1, Carbon::parse($plan->start_time)->diffInMinutes($plan->end_time) / 60),
                    'parent' => 'order-' . $machineId . '-' . $orderId,
                    'progress' => $plan->status === 'finished' ? 1 : 0
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


