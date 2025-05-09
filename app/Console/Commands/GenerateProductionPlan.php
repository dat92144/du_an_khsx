<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{
    ProductionOrder, ProductionPlan, MachineSchedule,
    ProductionHistory, Spec, BomItem
};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateProductionPlan extends Command
{
    protected $signature = 'app:generate-production-plan';
    protected $description = 'Sinh kế hoạch sản xuất cho tất cả ProductionOrder có trạng thái pending';

    public function handle()
    {
        $this->info("\n🔍 Đang tìm các ProductionOrders cần lập kế hoạch...");

        $orders = ProductionOrder::where('producing_status', 'pending')->get();

        if ($orders->isEmpty()) {
            $this->warn("✅ Không có ProductionOrder nào cần lập kế hoạch.");
            return 0;
        }

        foreach ($orders as $order) {
            $this->info("\n➡ Đang xử lý ProductionOrder: {$order->id}");

            try {
                // Gom các mối liên hệ từ bom_items
                $bomItems = BomItem::where('product_id', $order->product_id)->get();
                $linkedSemi = $bomItems->where('material_type', 'semi_finished_product')->pluck('input_material_id')->toArray();

                $relatedOrders = ProductionOrder::where('order_id', $order->order_id)
                    ->where('producing_status', 'pending')
                    ->get();

                $semiFirst = [];
                $productOnly = [];

                foreach ($relatedOrders as $rel) {
                    if ($rel->semi_finished_product_id) {
                        $semiFirst[$rel->semi_finished_product_id] = $rel;
                    } else {
                        $productOnly[] = $rel;
                    }
                }

                // 1. Sản xuất bán thành phẩm nếu có liên quan
                foreach ($semiFirst as $semiId => $semiOrder) {
                    if (in_array($semiId, $linkedSemi)) {
                        $this->generatePlanForOrder($semiOrder);
                        $semiOrder->update(['producing_status' => 'planned']);
                        $this->info("✔ Đã lập kế hoạch cho bán thành phẩm liên quan: {$semiOrder->id}");
                    }
                }

                // 2. Sản xuất sản phẩm chính sau khi bán thành phẩm sẵn sàng
                $this->generatePlanForOrder($order);
                $order->update(['producing_status' => 'planned']);
                $this->info("✔ Đã lập kế hoạch cho sản phẩm chính: {$order->id}");

                // 3. Các bán thành phẩm và sản phẩm không liên quan thì xử lý lần lượt sau
                foreach ($relatedOrders as $rel) {
                    if ($rel->producing_status === 'pending' && $rel->id !== $order->id) {
                        $this->generatePlanForOrder($rel);
                        $rel->update(['producing_status' => 'planned']);
                        $this->info("✔ Đã lập kế hoạch riêng cho: {$rel->id}");
                    }
                }

            } catch (\Throwable $e) {
                $this->error("❌ Lỗi khi xử lý {$order->id}: " . $e->getMessage());
            }
        }

        return 0;
    }

    private function generatePlanForOrder($order)
    {
        $targetId = $order->product_id ?? $order->semi_finished_product_id;
        $productType = $order->product_id ? 'product' : 'semi_finished_product';

        $specs = Spec::where($productType === 'product' ? 'product_id' : 'semi_finished_product_id', $targetId)
            ->orderBy('process_id')
            ->get();

        if ($specs->isEmpty()) {
            throw new \Exception("Không tìm thấy quy trình sản xuất (spec)");
        }

        $lotSize = $specs->first()->lot_size ?? 1;
        $totalQty = $order->order_quantity;
        $numLots = ceil($totalQty / $lotSize);
        $now = now();
        $startDate = $now->hour < 8 ? $now->copy()->setTime(8, 0) : $now;

        //$startDate = Carbon::now()->setTime(8, 0); // Bắt đầu ngày mới lúc 08:00

        for ($lot = 1; $lot <= $numLots; $lot++) {
            $lotQty = ($lot == $numLots) ? $totalQty - $lotSize * ($numLots - 1) : $lotSize;

            $stepStart = clone $startDate;

            foreach ($specs as $step) {
                $machineId = $step->machine_id;
                $cycleTime = $step->cycle_time ?? 0;
                $durationMinutes = ceil($cycleTime * $lotQty);

                // Không còn getAvailableTime vì phải tuần tự theo quy trình
                $remaining = $this->getRemainingCapacity($machineId, $stepStart);
                if ($remaining < $lotQty) {
                    $stepStart->addDay()->setTime(8, 0);
                    $lot--; // thử lại lô này ngày hôm sau
                    continue 2;
                }

                $startTime = clone $stepStart;
                $endTime = (clone $startTime)->addMinutes($durationMinutes);

                // Tạo lịch máy
                $scheduleId = $this->generateId('machine_schedules', 'MS');
                MachineSchedule::create([
                    'id' => $scheduleId,
                    'machine_id' => $machineId,
                    'production_order_id' => $order->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'scheduled'
                ]);

                // Tạo kế hoạch
                $planId = $this->generateId('production_plans', 'PLAN', 3, 'plan_id');
                ProductionPlan::create([
                    'plan_id' => $planId,
                    'order_id' => $order->order_id,
                    'product_id' => $order->product_id,
                    'semi_finished_product_id' => $order->semi_finished_product_id,
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

                // cập nhật thời gian bắt đầu cho bước kế tiếp
                $stepStart = clone $endTime;
            }

            // Lưu lịch sử và tồn kho như cũ
            $historyId = $this->generateId('production_histories', 'HIST');
            ProductionHistory::create([
                'id' => $historyId,
                'production_order_id' => $order->id,
                'product_id' => $order->product_id,
                'completed_quantity' => $lotQty,
                'date' => now(),
            ]);

            DB::table('inventories')->updateOrInsert(
                ['item_id' => $targetId],
                [
                    'item_type' => $productType,
                    'quantity' => DB::raw("quantity + $lotQty"),
                    'unit_id' => null
                ]
            );

            $bomItems = BomItem::where($productType === 'product' ? 'product_id' : 'semi_finished_product_id', $targetId)->get();
            foreach ($bomItems as $item) {
                $used = $item->quantity_input * $lotQty;
                DB::table('inventory_materials')
                    ->where('material_id', $item->input_material_id)
                    ->decrement('quantity', $used);
            }
        }
    }


    private function getRemainingCapacity($machineId, $date)
    {
        $capacity = DB::table('machine_capacity')
            ->where('machine_id', $machineId)
            ->value('max_output_per_day') ?? 999999;

        $scheduled = DB::table('machine_schedules')
            ->join('production_orders', 'machine_schedules.production_order_id', '=', 'production_orders.id')
            ->where('machine_schedules.machine_id', $machineId)
            ->whereDate('start_time', $date->toDateString())
            ->sum('production_orders.order_quantity');

        return $capacity - $scheduled;
    }

    private function getAvailableTime($machineId, $durationMinutes, $date)
    {
        $latest = DB::table('machine_schedules')
            ->where('machine_id', $machineId)
            ->whereDate('start_time', $date->toDateString())
            ->orderBy('end_time', 'desc')
            ->first();

        return $latest ? Carbon::parse($latest->end_time) : $date->copy()->setTime(8, 0);
    }

    private function generateId($table, $prefix, $length = 3, $idColumn = 'id')
    {
        $latest = DB::table($table)
            ->where($idColumn, 'like', $prefix . '%')
            ->orderBy($idColumn, 'desc')
            ->value($idColumn);

        $number = $latest ? intval(substr($latest, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($number, $length, '0', STR_PAD_LEFT);
    }
}
