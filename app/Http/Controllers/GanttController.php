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
                'duration' => max(1, Carbon::parse($order->delivery_date)->diffInDays($order->order_date)),
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
        $plans = DB::table('production_plans')->get();
        $tasks = [];
        $groupByProduct = $plans->groupBy('product_id');

        foreach ($groupByProduct as $productId => $productPlans) {
            $tasks[] = [
                'id' => 'prod-' . $productId,
                'text' => 'Sản phẩm ' . $productId,
                'start_date' => $productPlans->min('start_time'),
                'duration' => max(1, Carbon::parse($productPlans->max('end_time'))->diffInMinutes($productPlans->min('start_time')) / 60),
                'progress' => 0,
                'open' => false
            ];

            $lots = $productPlans->groupBy('lot_number');
            foreach ($lots as $lot => $lotPlans) {
                $lotId = 'lot-' . $productId . '-' . $lot;

                $tasks[] = [
                    'id' => $lotId,
                    'text' => 'Lô số ' . $lot,
                    'start_date' => $lotPlans->min('start_time'),
                    'duration' => max(1, Carbon::parse($lotPlans->max('end_time'))->diffInMinutes($lotPlans->min('start_time')) / 60),
                    'parent' => 'prod-' . $productId,
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

        $plans = DB::table('production_plans')
            ->where('product_id', $productId)
            ->where('lot_number', $lot)
            ->get();

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
            // Task cha: Máy
            $tasks[] = [
                'id' => 'machine-' . $machineId,
                'text' => 'Máy ' . $machineId,
                'start_date' => $machinePlans->min('start_time'),
                'duration' => max(1, \Carbon\Carbon::parse($machinePlans->max('end_time'))->diffInMinutes($machinePlans->min('start_time')) / 60),
                'progress' => 0,
                'open' => true
            ];

            foreach ($machinePlans as $plan) {
                $tasks[] = [
                    'id' => 'plan-' . $plan->plan_id,
                    'text' => "Lệnh {$plan->plan_id} ({$plan->process_id} - Lô {$plan->lot_number})",
                    'start_date' => $plan->start_time,
                    'duration' => max(1, \Carbon\Carbon::parse($plan->end_time)->diffInMinutes($plan->start_time) / 60),
                    'parent' => 'machine-' . $machineId,
                    'progress' => $plan->status === 'finished' ? 1 : 0
                ];
            }
        }

        return response()->json([
            'data' => $tasks,
            'links' => []
        ]);
    }
}


