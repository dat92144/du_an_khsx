<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionOrder;
use App\Models\Inventory;
use App\Models\SupplierPrice;
use App\Models\PurchaseRequests;
use App\Models\BomItem;
use App\Models\Spec;
use Carbon\Carbon;

class PurchaseSuggestionController extends Controller
{
    public function run()
    {
        DB::beginTransaction();
        try {
            $plannedOrders = ProductionOrder::whereIn('producing_status', ['planned', 'approved', 'pending'])
                ->whereNotNull('order_date')
                ->orderBy('order_date', 'asc')
                ->get();

            if ($plannedOrders->isEmpty()) {
                $this->handleStockOnly();
                DB::commit();
                return response()->json(['message' => '✅ Đã kiểm tra tồn kho tối thiểu']);
            }

            $ordersGroupedByOrderId = $plannedOrders->groupBy('order_id');

            foreach ($ordersGroupedByOrderId as $orderId => $orders) {
                foreach ($orders as $order) {
                    $productId = $order->product_id;
                    $orderQuantity = $order->order_quantity;
                    $startDate = Carbon::parse($order->order_date);

                    $productInventory = Inventory::where('item_id', $productId)->where('item_type', 'product')->first();
                    $specs = Spec::where('product_id', $productId)->get();
                    $productCycleTime = $specs->sum('cycle_time');
                    $productPerDay = $productCycleTime > 0 ? (8 * 60 / $productCycleTime) : 0;

                    $bomItems = BomItem::where('bom_id', $order->bom_id)->get();
                    foreach ($bomItems as $bomItem) {
                        $materialId = $bomItem->input_material_id;
                        $inputType = $bomItem->input_material_type;
                        $remainingQty = max(0, $orderQuantity - ($productInventory->quantity ?? 0));
                        $requiredQty = $bomItem->quantity_input * $remainingQty;

                        $inventory = Inventory::where('item_id', $materialId)->where('item_type', $inputType)->first();
                        $currentStock = $inventory ? $inventory->quantity : 0;
                        $dayOut = $productPerDay > 0 ? ($currentStock / ($bomItem->quantity_input * $productPerDay)) : 0.1;

                        if ($inputType === 'materials') {
                            $this->handleMaterial($materialId, $requiredQty, $currentStock, $dayOut);
                        } elseif ($inputType === 'semi_finished_products') {
                            $this->handleSemiFinished($materialId, $requiredQty, $currentStock, $dayOut);
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => '✅ Đã đề xuất mua hàng dựa trên kế hoạch sản xuất']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function handleStockOnly()
    {
        $inventories = Inventory::whereIn('item_type', ['material', 'semi_finished_product'])->get();
        foreach ($inventories as $item) {
            $itemId = $item->item_id;
            $minStock = $item->min_stock ?? 100;
            $currentStock = $item->quantity ?? 0;
            $shortage = max(0, $minStock - $currentStock);

            if ($shortage <= 0) continue;

            if ($item->item_type === 'semi_finished_product') {
                $canProduce = BomItem::where('output_id', $itemId)->first();
                if ($canProduce) continue;
            }

            $bestSupplier = SupplierPrice::where('material_id', $itemId)->orderBy('delivery_time')->first();
            if (!$bestSupplier) continue;
            PurchaseRequests::create([
                'supplier_id' => $bestSupplier->supplier_id,
                'material_id' => $itemId,
                'type' => $item->item_type,
                'quantity' => $shortage,
                'unit_id' => $bestSupplier->unit_id,
                'price_per_unit' => $bestSupplier->price_per_unit,
                'total_price' => $shortage * $bestSupplier->price_per_unit,
                'expected_delivery_date' => Carbon::now()->addDays($bestSupplier->delivery_time ?? 3),
                'status' => 'pending'
            ]);
        }
    }

    private function handleMaterial($materialId, $required, $stock, $dayOut)
    {
        $ordered = DB::table('purchase_orders')
            ->where('material_id', $materialId)
            ->where('type', 'material')
            ->whereIn('status', ['pending', 'ordered'])
            ->sum('quantity');

        $shortage = max(0, $required - $stock - $ordered);
        if ($shortage <= 0) return;

        $supplier = $dayOut < 1
            ? SupplierPrice::where('material_id', $materialId)->orderBy('delivery_time')->first()
            : SupplierPrice::where('material_id', $materialId)->where('delivery_time', '<', $dayOut)->orderBy('price_per_unit')->first();

        if (!$supplier) return;

        PurchaseRequests::create([
            'supplier_id' => $supplier->supplier_id,
            'material_id' => $materialId,
            'type' => 'material',
            'quantity' => $shortage,
            'unit_id' => $supplier->unit_id,
            'price_per_unit' => $supplier->price_per_unit,
            'total_price' => $shortage * $supplier->price_per_unit,
            'expected_delivery_date' => Carbon::now()->addDays($supplier->delivery_time ?? 3),
            'status' => 'pending'
        ]);
    }

    private function handleSemiFinished($materialId, $required, $stock, $dayOut)
    {
        $canProduce = BomItem::where('output_id', $materialId)->first();
        if ($canProduce) return;

        $ordered = DB::table('purchase_orders')
            ->where('material_id', $materialId)
            ->where('type', 'semi_finished_product')
            ->whereIn('status', ['pending', 'ordered'])
            ->sum('quantity');

        $shortage = max(0, $required - $stock - $ordered);
        if ($shortage <= 0) return;

        $supplier = $dayOut < 1
            ? SupplierPrice::where('material_id', $materialId)->orderBy('delivery_time')->first()
            : SupplierPrice::where('material_id', $materialId)->where('delivery_time', '<', $dayOut)->orderBy('price_per_unit')->first();

        if (!$supplier) return;

        PurchaseRequests::create([
            'supplier_id' => $supplier->supplier_id,
            'material_id' => $materialId,
            'type' => 'semi_finished_product',
            'quantity' => $shortage,
            'unit_id' => $supplier->unit_id,
            'price_per_unit' => $supplier->price_per_unit,
            'total_price' => $shortage * $supplier->price_per_unit,
            'expected_delivery_date' => Carbon::now()->addDays($supplier->delivery_time ?? 3),
            'status' => 'pending'
        ]);
    }
}
