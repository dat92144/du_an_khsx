<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{
    ProductionOrder, ProductionPlan, MachineSchedule, Spec, BomItem
};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateProductionPlan extends Command
{
    protected $signature = 'app:generate-production-plan';
    protected $description = 'Sinh kế hoạch sản xuất cho tất cả ProductionOrder có trạng thái approved';

    public function handle()
    {
        $this->info("\n🔍 Đang tìm các ProductionOrders cần lập kế hoạch...");

        $orders = ProductionOrder::where('producing_status', 'approved')->get();
        if ($orders->isEmpty()) {
            $this->warn("✅ Không có ProductionOrder nào cần lập kế hoạch.");
            return 0;
        }

        $scheduleMap = MachineSchedule::where('end_time', '>', now())->get()->groupBy('machine_id');

        foreach ($orders as $order) {
            $this->info("\n➞ Đang xử lý ProductionOrder: {$order->id}");

            try {
                $startTime = microtime(true);

                $bomItems = $order->product_id
                    ? BomItem::where('product_id', $order->product_id)->get()
                    : BomItem::where('semi_finished_product_id', $order->semi_finished_product_id)->get();

                $linkedSemi = $bomItems->where('material_type', 'semi_finished_product')->pluck('input_material_id')->toArray();

                $relatedOrders = ProductionOrder::where('order_id', $order->order_id)
                    ->where('producing_status', 'pending')->get();

                $semiFirst = [];
                foreach ($relatedOrders as $rel) {
                    if ($rel->semi_finished_product_id) {
                        $semiFirst[$rel->semi_finished_product_id] = $rel;
                    }
                }

                foreach ($semiFirst as $semiId => $semiOrder) {
                    if (in_array($semiId, $linkedSemi)) {
                        $this->generatePlanForOrder($semiOrder, $scheduleMap);
                        $semiOrder->update(['producing_status' => 'planned']);
                        $this->info("✔ Đã lập kế hoạch cho bán thành phẩm liên quan: {$semiOrder->id}");
                    }
                }

                $this->generatePlanForOrder($order, $scheduleMap);
                $order->update(['producing_status' => 'planned']);
                $this->info("✔ Đã lập kế hoạch cho sản phẩm chính: {$order->id}");

                foreach ($relatedOrders as $rel) {
                    if ($rel->producing_status === 'pending' && $rel->id !== $order->id) {
                        $this->generatePlanForOrder($rel, $scheduleMap);
                        $rel->update(['producing_status' => 'planned']);
                        $this->info("✔ Đã lập kế hoạch riêng cho: {$rel->id}");
                    }
                }

                $duration = round(microtime(true) - $startTime, 2);
                $this->line("⏱️  Thời gian xử lý: {$duration} giây\n");

            } catch (\Throwable $e) {
                $this->error("❌ Lỗi khi xử lý {$order->id}: " . $e->getMessage());
            }
        }

        return 0;
    }

    private function generatePlanForOrder($order, $scheduleMap)
    {
        $targetId = $order->product_id ?? $order->semi_finished_product_id;
        $productType = $order->product_id ? 'product' : 'semi_finished_product';

        $specs = Spec::where($productType === 'product' ? 'product_id' : 'semi_finished_product_id', $targetId)
            ->orderBy('process_id')->get();

        if ($specs->isEmpty()) {
            throw new \Exception("❌ Không tìm thấy quy trình sản xuất cho {$productType}: $targetId");
        }

        $lotSize = $specs->first()->lot_size ?? 1;
        $totalQty = $order->order_quantity;

        $unit_order = DB::table('order_details')
            ->where('order_id', $order->order_id)
            ->where(function ($q) use ($order) {
                $order->product_id
                    ? $q->where('product_id', $order->product_id)
                    : $q->where('semi_finished_product_id', $order->semi_finished_product_id);
            })->value('unit_id');

        $unit = DB::table('units')->where('id', $unit_order)->first();
        if ($unit && $unit->name === 'Bao') {
            $this->line("⚖️ Quy đổi $totalQty bao → " . ($totalQty * 0.05) . " tấn");
            $totalQty *= 0.05;
        }

        $bomItems = BomItem::where($productType === 'product' ? 'product_id' : 'semi_finished_product_id', $targetId)->get();
        $materialOk = true;

        foreach ($bomItems as $item) {
            $previousProcess = BomItem::where('output_id', $item->input_material_id)->first();
            if ($previousProcess) {
                $this->info("Bán thành phẩm $item->input_material_id có thể sản xuất trong công đoạn {$previousProcess->process_id}.");
                continue;
            } else {
                $requiredTotal = $item->quantity_input * $totalQty;
                $stock = DB::table('inventories')->where('item_id', $item->input_material_id)
                    ->where('item_type', $item->input_material_type)->value('quantity') ?? 0;
                $incoming = DB::table('purchase_orders')->where('material_id', $item->input_material_id)
                    ->where('status', 'ordered')->sum('quantity');

                $available = $stock + $incoming;
                if ($available < $requiredTotal) {
                    $this->warn("❌ Không đủ nguyên liệu {$item->input_material_id}: cần $requiredTotal, có $available");
                    $materialOk = false;
                }
            }
        }

        if (!$materialOk) {
            throw new \Exception("⛔ Không đủ nguyên vật liệu để lập kế hoạch cho đơn {$order->id}");
        }

        $numLots = ceil($totalQty / $lotSize);
        $startDate = now();
        $this->line("📦 Tổng số lô: $numLots | Kích cỡ lô: $lotSize");

        $lastEndTime = null;
        $loggedMachines = [];

        for ($lot = 1; $lot <= $numLots; $lot++) {
            $lotQty = ($lot == $numLots) ? $totalQty - $lotSize * ($numLots - 1) : $lotSize;
            $stepStart = clone $startDate;

            $this->line("➞️ Lô $lot/$numLots | Số lượng: $lotQty");

            foreach ($specs as $step) {
                $machineId = $step->machine_id;
                $cycleTime = $step->cycle_time ?? 0;
                $durationMinutes = ceil($cycleTime * $lotQty);
                $maxWaitDays = 30;
                $waited = 0;

                while ($this->isMachineBusyCached($machineId, $stepStart, $durationMinutes, $scheduleMap)) {
                    $logKey = $machineId . '|' . $stepStart->toDateString();
                    if (!isset($loggedMachines[$logKey])) {
                        $this->line("⏳ Máy $machineId đang bận vào " . $stepStart->toDateTimeString() . ", thử lại ngày mai...");
                        $loggedMachines[$logKey] = true;
                    }
                    $stepStart->addDay();
                    $waited++;
                    if ($waited > $maxWaitDays) {
                        throw new \Exception("⛔ Máy {$machineId} bận suốt $maxWaitDays ngày, không thể lập kế hoạch.");
                    }
                }

                $startTime = clone $stepStart;
                $endTime = (clone $startTime)->addMinutes($durationMinutes);
                $lastEndTime = $endTime;

                MachineSchedule::create([
                    'machine_id' => $machineId,
                    'production_order_id' => $order->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'scheduled'
                ]);

                ProductionPlan::create([
                    'order_id' => $order->order_id,
                    'product_id' => $productType === 'product' ? $targetId : null,
                    'semi_finished_product_id' => $productType === 'semi_finished_product' ? $targetId : null,
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

                $stepStart = clone $endTime;
            }

            $startDate = clone $stepStart;
        }

        if ($lastEndTime && Carbon::parse($order->delivery_date)->lt($lastEndTime)) {
            $this->warn("🚨 Cảnh báo: đơn {$order->id} có thể trễ giao hàng! Kết thúc dự kiến: " . $lastEndTime->format('Y-m-d H:i'));
        }

        $this->line("✅ Kế hoạch kết thúc vào: " . $lastEndTime?->format('Y-m-d H:i'));
    }

    private function isMachineBusyCached($machineId, $start, $durationMinutes, $schedulesByMachine)
    {
        $end = (clone $start)->addMinutes($durationMinutes);

        if (!isset($schedulesByMachine[$machineId])) return false;

        foreach ($schedulesByMachine[$machineId] as $schedule) {
            if (
                $schedule->start_time < $end &&
                $schedule->end_time > $start
            ) {
                return true;
            }
        }

        return false;
    }
}
