<?php

namespace App\Console\Commands;

use App\Models\BomItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionOrder;
use Carbon\Carbon;
use App\Models\Inventory;
use App\Models\SupplierPrice;
use App\Models\Process; // Kiểm tra công đoạn sản xuất trước đó
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequests;
use App\Models\Spec;

class AutoPurchaseByProductionPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchase:auto-production';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tu dong de xuat mua hang';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();

        try {
            $plannedOrders = ProductionOrder::whereIn('producing_status', ['planned', 'approved', 'pending'])
                ->whereNotNull('order_date')
                ->orderBy('order_date', 'asc')
                ->get();
            if ($plannedOrders->isEmpty()) {
                $this->info("Không có KHSX nào đủ điều kiện để đề xuất mua hàng.");
                return;
            }

            foreach ($plannedOrders as $order) {
                $productId = $order->product_id;
                $orderQuantity = $order->order_quantity;
                $startDate = Carbon::parse($order->order_date);
                $productInventory = Inventory::where('item_id', $productId)
                    ->where('item_type', 'product')->first();
                $specs = Spec::where('product_id', $productId)->get();
                $productCycleTime = 0;
                foreach($specs as $spec){
                    $productCycleTime += $spec->cycle_time;
                }         
                $productPerDay = 8*60/$productCycleTime;
                $bomItems = BomItem::where('bom_id', $order->bom_id)->get();
                foreach ($bomItems as $bomItem) {
                    $materialId = $bomItem->input_material_id;
                    $inputType = $bomItem->input_material_type; 
                    $remainingOrderQuantity = max(0, $orderQuantity - ($productInventory->quantity ?? 0));
                    $requiredQuantity = $bomItem->quantity_input * $remainingOrderQuantity;
                    $inventory = Inventory::where('item_id', $materialId)
                        ->where('item_type', $inputType)
                        ->first();
                    $currentStock = $inventory ? $inventory->quantity : 1;
                    $DayOfStockOut = $currentStock/($bomItem->quantity_input*$productPerDay);
                    if ($inputType === 'materials') {
                        $this->info("Dang o day");
                        $this->handleMaterialPurchase($materialId, $requiredQuantity, $currentStock, $startDate, $DayOfStockOut);
                    }
                    else if ($inputType === 'semi_finished_products') {
                        $this->handleSemiFinishedProduct($materialId, $requiredQuantity, $currentStock, $startDate, $DayOfStockOut);
                    }
                }
            }

            DB::commit();
            $this->info('Đã hoàn thành đề xuất mua hàng và kiểm tra bán thành phẩm.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Lỗi khi chạy lệnh: ' . $e->getMessage());
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
            
            if($DayOfStockOut < 1){
                $bestSupplier = SupplierPrice::where('material_id', $materialId)
                    ->orderBy('delivery_time', 'asc')
                    ->first();
            }else{
                $bestSupplier = SupplierPrice::where('material_id', $materialId)
                    ->where('delivery_time', '<', $DayOfStockOut)
                    ->orderBy('price_per_unit', 'asc')
                    ->first();
            }
            
            if ($bestSupplier) {
                $this->info("$bestSupplier");
                PurchaseRequests::create([
                    'supplier_id' => $bestSupplier->supplier_id,
                    'material_id' => $materialId,
                    'type'=> 'material',
                    'quantity' => $actualShortage,
                    'unit_id' => $bestSupplier->unit_id,
                    'price_per_unit' => $bestSupplier->price_per_unit,
                    'total_price' => $actualShortage * $bestSupplier->price_per_unit,
                    'expected_delivery_date' => Carbon::now()->addDays($DayOfStockOut),
                    'status' => 'pending'
                ]);

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
            
            if($DayOfStockOut < 1){
                $bestSupplier = SupplierPrice::where('material_id', $materialId)
                    ->orderBy('delivery_time', 'asc')
                    ->first();
            }else{
                $bestSupplier = SupplierPrice::where('material_id', $materialId)
                    ->where('delivery_time', '<', $DayOfStockOut)
                    ->orderBy('price_per_unit', 'asc')
                    ->first();
            }

        if ($bestSupplier) {

            PurchaseRequests::create([
                'supplier_id' => $bestSupplier->supplier_id,
                'material_id' => $materialId,
                'type' => 'semi_finished_product',
                'quantity' => $requiredQuantity,
                'unit_id' => $bestSupplier->unit_id,
                'price_per_unit' => $bestSupplier->price_per_unit,
                'total_price' => $requiredQuantity * $bestSupplier->price_per_unit,
                'expected_delivery_date' => $DayOfStockOut,
                'status' => 'pending'
            ]);

            $this->info("Đặt mua bán thành phẩm $materialId, số lượng $requiredQuantity, giao hàng trước ngày: $DayOfStockOut");
        } else {
            $this->warn("Không thể mua bán thành phẩm $materialId từ nhà cung cấp.");
        }
    }}

}