<?php

namespace App\Console\Commands;

use App\Models\BomItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionOrder;
use Carbon\Carbon;
use App\Models\Inventory;
use App\Models\SupplierPrice;
use App\Models\Process;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequests;
use App\Models\Spec;
use Illuminate\Support\Facades\Http;

class AutoPurchaseByProductionPlan extends Command
{
    protected $signature = 'purchase:auto-production';
    protected $description = 'Tu dong de xuat mua hang';
    protected function notifyNewPurchaseRequest($request)
    {
        try {
            Http::post("http://localhost:3001/notify", [
                'type' => 'created',
                'request' => $request
            ]);
        } catch (\Exception $e) {
            $this->error("❌ Không thể gửi WebSocket event: " . $e->getMessage());
        }
    }
    public function handle()
    {
        DB::beginTransaction();

        try {
            $plannedOrders = ProductionOrder::whereIn('producing_status', ['pending'])
                ->whereNotNull('order_date')
                ->orderBy('order_date', 'asc')
                ->get();

            if ($plannedOrders->isEmpty()) {
                $this->info("Không có KHSX nào đủ điều kiện để đề xuất mua hàng.");
                $this->info("🔍 Không có kế hoạch sản xuất → kiểm tra min_stock");
                $this->handleStock();
                DB::commit();
                return;
            }

            $ordersGroupedByOrderId = $plannedOrders->groupBy('order_id');

            foreach ($ordersGroupedByOrderId as $orderId => $orders) {
                $this->info("👉 Đang xử lý đề xuất mua hàng cho đơn hàng Order ID: $orderId");

                foreach ($orders as $order) {
                    $productId = $order->product_id ?? $order->semi_finished_product_id;
                    $productType = $order->product_id ? 'product' : 'semi_finished_product';
                    $unit_order = DB::table('order_details')
                        ->where('order_id', $order->order_id)
                        ->where(function ($query) use ($productId) {
                            $query->where('product_id', $productId)
                                ->orWhere('semi_finished_product_id', $productId);
                        })
                        ->value('unit_id');
                    $unit = DB::table('units')->where('id', $unit_order)->first();
                    $this->info("Đây là đơn vị: $unit->name");
                    $this->info("Đây là sản phẩm: $productId");
                    $orderQuantity = $order->order_quantity;
                    if ($unit->name === 'Bao') {
                        $orderQuantity = $orderQuantity * 0.05;
                        $this->info("⚖️ Quy đổi đơn vị từ bao sang tấn: {$order->order_quantity} bao → $orderQuantity tấn");
                    }
                    $startDate = Carbon::parse($order->order_date);

                    $productInventory = Inventory::where('item_id', $productId)
                        ->where('item_type', $productType)
                        ->first();
                    $productInventoryQty = $productInventory ? $productInventory->quantity : 0;

                    $productInventoryInTon = ($productType === 'product')
                        ? ($productInventoryQty * 0.05)
                        : $productInventoryQty;
                    $specs = Spec::where('product_id', $productId)->get();
                    $productCycleTime = $specs->sum('cycle_time');
                    $productPerDay = $productCycleTime > 0 ? (24 * 60 / $productCycleTime) : 0;

                    $bomItems = BomItem::where('bom_id', $order->bom_id)->get();

                    foreach ($bomItems as $bomItem) {
                        $materialId = $bomItem->input_material_id;
                        $inputType = $bomItem->input_material_type;
                        $remainingOrderQuantity = max(0, $orderQuantity - $productInventoryInTon);
                        $requiredQuantity = $bomItem->quantity_input/100 * $remainingOrderQuantity;

                        $inventory = Inventory::where('item_id', $materialId)
                            ->where('item_type', $inputType)
                            ->first();
                        $currentStock = $inventory ? $inventory->quantity : 0;
                        $DayOfStockOut = $productPerDay > 0 ? ($currentStock / ($bomItem->quantity_input * $productPerDay)) : 0.1;

                        if ($inputType === 'materials') {
                            $this->handleMaterialPurchase($materialId, $requiredQuantity, $currentStock, $startDate, $DayOfStockOut);
                        } elseif ($inputType === 'semi_finished_products') {
                            $this->handleSemiFinishedProduct($materialId, $requiredQuantity, $currentStock, $startDate, $DayOfStockOut);
                        }
                    }
                }
            }

            DB::commit();
            $this->info('✅ Đã hoàn thành đề xuất mua hàng và kiểm tra bán thành phẩm.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Lỗi khi chạy lệnh: ' . $e->getMessage());
        }
    }

    private function handleStock()
    {
        $allInventory = Inventory::whereIn('item_type', ['material', 'semi_finished_product'])->get();

        foreach ($allInventory as $item) {
            $itemId = $item->item_id;
            $itemType = $item->item_type;
            $minStock = $item->min_stock ?? 100;
            $currentStock = $item->quantity ?? 0;
            $shortage = max(0, $minStock - $currentStock);

            if ($shortage <= 0) {
                $this->info("✅ $itemId đã đủ min_stock ($currentStock / $minStock)");
                continue;
            }

            if ($itemType === 'semi_finished_product') {
                $previousProcess = BomItem::where('output_id', $itemId)->first();
                if ($previousProcess) {
                    $this->info("🔁 $itemId có thể sản xuất trong công đoạn {$previousProcess->process_id}, không cần mua.");
                    continue;
                }
            }

            $bestSupplier = SupplierPrice::where('material_id', $itemId)
                ->orderBy('delivery_time', 'asc')
                ->first();

            if (!$bestSupplier) {
                $this->warn("⚠️ Không tìm thấy nhà cung cấp cho $itemId.");
                continue;
            }

            $exists = PurchaseRequests::where('material_id', $itemId)
                ->where('type', $itemType)
                ->where('status', 'pending')
                ->exists();

            if ($exists) {
                $this->warn("⚠️ Đã có đề xuất mua $itemId ($itemType) đang chờ xử lý. Bỏ qua.");
                continue;
            }

            try {
                $request = PurchaseRequests::create([
                    'supplier_id' => $bestSupplier->supplier_id,
                    'material_id' => $itemId,
                    'type' => $itemType,
                    'quantity' => $shortage,
                    'unit_id' => $bestSupplier->unit_id,
                    'price_per_unit' => $bestSupplier->price_per_unit,
                    'total_price' => $shortage * $bestSupplier->price_per_unit,
                    'expected_delivery_date' => Carbon::now()->addDays($bestSupplier->delivery_time ?? 3)->toDateTimeString(),
                    'status' => 'pending',
                ]);
                $this->notifyNewPurchaseRequest($request);
                $this->info("📥 Đề xuất mua $itemId ($itemType) - thiếu $shortage cái - từ nhà cung cấp {$bestSupplier->supplier_id}");
            } catch (\Exception $e) {
                $this->error("❌ Lỗi khi tạo đề xuất mua $itemId: " . $e->getMessage());
            }
        }
    }

    private function handleMaterialPurchase($materialId, $requiredQuantity, $currentStock, $startDate, $DayOfStockOut)
    {
        $orderedMaterial = PurchaseOrder::where('material_id', $materialId)
            ->whereIn('status', ['pending', 'ordered'])
            ->where('type', 'material')
            ->sum('quantity');
        $this->info("số nguyên vật liệu đang về: $orderedMaterial");

        $actualShortage = max(0, $requiredQuantity - $currentStock - $orderedMaterial);
        $this->info("Kiểm tra thiếu hụt nguyên vật liệu: $materialId - Thiếu: $actualShortage");

        if ($actualShortage > 0) {
            $exists = PurchaseRequests::where('material_id', $materialId)
                ->where('type', 'material')
                ->where('status', 'pending')
                ->exists();

            if ($exists) {
                $this->warn("⚠️ Đã có đề xuất mua $materialId (material) đang chờ xử lý. Bỏ qua.");
                return;
            }

            $bestSupplier = $DayOfStockOut < 1
                ? SupplierPrice::where('material_id', $materialId)->orderBy('delivery_time', 'asc')->first()
                : SupplierPrice::where('material_id', $materialId)
                    ->where('delivery_time', '<', $DayOfStockOut)
                    ->orderBy('price_per_unit', 'asc')
                    ->first();

            if ($bestSupplier) {
                $this->info("$bestSupplier");
                $request = PurchaseRequests::create([
                    'supplier_id' => $bestSupplier->supplier_id,
                    'material_id' => $materialId,
                    'type' => 'material',
                    'quantity' => $actualShortage,
                    'unit_id' => $bestSupplier->unit_id,
                    'price_per_unit' => $bestSupplier->price_per_unit,
                    'total_price' => $actualShortage * $bestSupplier->price_per_unit,
                    'expected_delivery_date' => Carbon::now()->addDays($DayOfStockOut),
                    'status' => 'pending'
                ]);
                $this->notifyNewPurchaseRequest($request);
                $this->info("Đặt mua nguyên vật liệu $materialId, số lượng $actualShortage, giao hàng trước ngày: " . Carbon::now()->addDays($DayOfStockOut)->format('d-m-Y'));
            } else {
                $this->warn("Không tìm thấy nhà cung cấp phù hợp cho nguyên vật liệu $materialId");
            }
        } else {
            $this->info("Nguyên vật liệu $materialId đã đủ, không cần đặt thêm.");
        }
    }

    private function handleSemiFinishedProduct($materialId, $requiredQuantity, $currentStock, $startDate, $DayOfStockOut)
    {
        $previousProcess = BomItem::where('output_id', $materialId)->first();

        if ($previousProcess) {
            $this->info("Bán thành phẩm $materialId có thể sản xuất trong công đoạn {$previousProcess->process_id}.");
            return;
        }

        $orderedMaterial = PurchaseOrder::where('material_id', $materialId)
            ->whereIn('status', ['pending', 'ordered'])
            ->where('type', 'semi_finished_product')
            ->sum('quantity');

        $actualShortage = max(0, $requiredQuantity - $currentStock - $orderedMaterial);

        if ($actualShortage > 0) {
            $exists = PurchaseRequests::where('material_id', $materialId)
                ->where('type', 'semi_finished_product')
                ->where('status', 'pending')
                ->exists();

            if ($exists) {
                $this->warn("⚠️ Đã có đề xuất mua $materialId (semi_finished_product) đang chờ xử lý. Bỏ qua.");
                return;
            }

            $bestSupplier = $DayOfStockOut < 1
                ? SupplierPrice::where('material_id', $materialId)->orderBy('delivery_time', 'asc')->first()
                : SupplierPrice::where('material_id', $materialId)
                    ->where('delivery_time', '<', $DayOfStockOut)
                    ->orderBy('price_per_unit', 'asc')
                    ->first();

            if ($bestSupplier) {
                $request = PurchaseRequests::create([
                    'supplier_id' => $bestSupplier->supplier_id,
                    'material_id' => $materialId,
                    'type' => 'semi_finished_product',
                    'quantity' => $requiredQuantity,
                    'unit_id' => $bestSupplier->unit_id,
                    'price_per_unit' => $bestSupplier->price_per_unit,
                    'total_price' => $requiredQuantity * $bestSupplier->price_per_unit,
                    'expected_delivery_date' => Carbon::now()->addDays($DayOfStockOut),
                    'status' => 'pending'
                ]);
                $this->notifyNewPurchaseRequest($request);
                $this->info("Đặt mua bán thành phẩm $materialId, số lượng $requiredQuantity, giao hàng trước ngày: $DayOfStockOut");
            } else {
                $this->warn("Không thể mua bán thành phẩm $materialId từ nhà cung cấp.");
            }
        }else {
            $this->info("Nguyên vật liệu $materialId đã đủ, không cần đặt thêm.");
        }
    }
}
