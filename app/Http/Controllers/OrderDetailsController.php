<?php

namespace App\Http\Controllers;

use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderDetailsController extends Controller {
    public function index(Request $request) {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return response()->json(['message' => 'Missing order_id'], 400);
        }

        $items = OrderDetails::where('order_id', $orderId)->get();
        
        if ($items->isEmpty()) {
            return response()->json(['message' => 'No items found'], 404);
        }

        return response()->json($items);
    }

    public function show($id) {
        $orderDetails = OrderDetails::find($id);
        if (!$orderDetails) return response()->json(['message' => 'Order Details not found'], 404);
        return response()->json($orderDetails, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'order_id' => 'required|string|exists:orders,id',
            'product_id' => 'required|string|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'required|string|exists:units,id'
        ]);

        $latest = DB::table('order_details')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'od' . str_pad(intval(substr($latest->id, 11)) + 1, 3, '0', STR_PAD_LEFT) : 'od001';
    
        $validated['id'] = $newId;
        $orderDetails = OrderDetails::create($validated);
    
        return response()->json($orderDetails, 201);
    }
    

    public function update(Request $request, $id) {
        $orderDetails = OrderDetails::find($id);
        if (!$orderDetails) return response()->json(['message' => 'Order Details not found'], 404);

        $validated = $request->validate([
            'order_id' => 'string|exists:orders,id',
            'product_id' => 'string|exists:products,id',
            'quantity' => 'integer',
            'unit_id' => 'string|exists:units,id'
        ]);

        $orderDetails->update($validated);
        return response()->json($orderDetails, 200);
    }

    public function destroy($id) {
        $orderDetails = OrderDetails::find($id);
        if (!$orderDetails) return response()->json(['message' => 'Order Details not found'], 404);

        $orderDetails->delete();
        return response()->json(['message' => 'Order Details deleted successfully'], 200);
    }
}
