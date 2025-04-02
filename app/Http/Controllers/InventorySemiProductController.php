<?php

namespace App\Http\Controllers;

use App\Models\InventorySemiProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InventorySemiProductController extends Controller {
    public function index()
    {
        $semiProducts = InventorySemiProduct::join('semi_finished_products', 'inventory_semi_products.semi_product_id', '=', 'semi_finished_products.id')
            ->join('units', 'inventory_semi_products.unit_id', '=', 'units.id')
            ->select(
                'inventory_semi_products.id',
                'semi_finished_products.name as semi_product_name', // Lấy đúng tên bán thành phẩm
                'inventory_semi_products.quantity',
                'units.name as unit_name'
            )
            ->get();
    
        return response()->json($semiProducts, 200);
    }    

    public function show($id) {
        $inventory = InventorySemiProduct::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Semi Product not found'], 404);
        return response()->json($inventory, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'semi_product_id' => 'required|string|exists:semi_finished_products,id',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'required|string|exists:units,id'
        ]);

        $latest = DB::table('inventory_semi_products')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'INVSP' . str_pad(intval(substr($latest->id, 5)) + 1, 3, '0', STR_PAD_LEFT) : 'INVSP001';
    
        $validated['id'] = $newId;
        $inventorySemiProduct = InventorySemiProduct::create($validated);
    
        return response()->json($inventorySemiProduct, 201);
    }    

    public function update(Request $request, $id) {
        $inventory = InventorySemiProduct::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Semi Product not found'], 404);

        $validated = $request->validate([
            'quantity' => 'integer|min:0',
            'unit_id' => 'exists:units,id'
        ]);

        $inventory->update($validated);
        return response()->json($inventory, 200);
    }

    public function destroy($id) {
        $inventory = InventorySemiProduct::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Semi Product not found'], 404);

        $inventory->delete();
        return response()->json(['message' => 'Inventory Semi Product deleted successfully'], 200);
    }
}
