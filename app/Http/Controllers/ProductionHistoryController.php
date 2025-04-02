<?php

namespace App\Http\Controllers;

use App\Models\ProductionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductionHistoryController extends Controller {
    public function index() {
        return response()->json(ProductionHistory::all(), 200);
    }

    public function show($id) {
        $history = ProductionHistory::find($id);
        if (!$history) return response()->json(['message' => 'Production History not found'], 404);
        return response()->json($history, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'product_id' => 'required|exists:products,id',
            'completed_quantity' => 'required|integer|min:0',
            'date' => 'required|date',
        ]);
    
        $latest = DB::table('production_histories')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'HIST' . str_pad(intval(substr($latest->id, 4)) + 1, 3, '0', STR_PAD_LEFT) : 'HIST001';
    
        $validated['id'] = $newId;
        $history = ProductionHistory::create($validated);
    
        return response()->json($history, 201);
    }    

    public function update(Request $request, $id) {
        $history = ProductionHistory::find($id);
        if (!$history) return response()->json(['message' => 'Production History not found'], 404);

        $history->update($request->all());
        return response()->json($history, 200);
    }

    public function destroy($id) {
        $history = ProductionHistory::find($id);
        if (!$history) return response()->json(['message' => 'Production History not found'], 404);

        $history->delete();
        return response()->json(['message' => 'Production History deleted successfully'], 200);
    }
}
