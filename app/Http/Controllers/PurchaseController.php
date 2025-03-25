<?php

namespace App\Http\Controllers;

use App\Models\OrderMaterials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function caculateMRP(){
        $ordermaterials=[];
        $orders = DB::table('purchase_orders')
            ->where('status', 'pending')->get();

        foreach($orders as $order){
            if (!isset($order->status)) {
                continue;
            }
            $details = DB::table('order_details')
                ->where('order_id', $order->order_id)
                ->get();
            
            foreach($details as $pm){
                $productmaterials=DB::table('product_materials')
                    ->where('product_id', $pm->product_id)->get();

                foreach($productmaterials as $cre){
                    $quantity = $cre->quantity * $pm->quantity;

                // Kiểm tra xem nguyên vật liệu này đã tồn tại trong `order_materials` chưa
                    $existingMaterial = DB::table('order_materials')
                        ->where('order_id', $order->order_id)
                        ->where('material_id', $cre->material_id)
                        ->first();
                        
                        $ordermaterials[$order->order_id][$cre->material_id] = $existingMaterial 
                        ? $existingMaterial->quantity + $quantity 
                        : $quantity;
                }
            }
        }
        return response()->json($ordermaterials);

    }
}
