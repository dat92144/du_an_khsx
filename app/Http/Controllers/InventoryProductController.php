<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InventoryProductController extends Controller {
    public function index()
    {
        $products = InventoryProduct::join('products', 'inventory_products.product_id', '=', 'products.id')
            ->join('units', 'inventory_products.unit_id', '=', 'units.id')
            ->select(
                'inventory_products.id',
                'products.name as product_name', // Lấy tên sản phẩm
                'inventory_products.quantity',
                'units.name as unit_name' // Lấy tên đơn vị
            )
            ->get();
    
        return response()->json($products, 200);
    }    

    public function show($id) {
        $inventory = InventoryProduct::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Product not found'], 404);
        return response()->json($inventory, 200);
    }

    public function getProducts()
    {
        $products = InventoryProduct::join('units', 'inventory_products.unit_id', '=', 'units.id')
            ->select('inventory_products.*', 'units.name as unit_name')
            ->get();

        return response()->json($products);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|string|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'required|string|exists:units,id'
        ]);
    
        $latest = DB::table('inventory_products')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'INVP' . str_pad(intval(substr($latest->id, 4)) + 1, 3, '0', STR_PAD_LEFT) : 'INVP001';
    
        $validated['id'] = $newId;
        $inventoryProduct = InventoryProduct::create($validated);
    
        return response()->json($inventoryProduct, 201);
    }    

    public function update(Request $request, $id) {
        $inventory = InventoryProduct::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Product not found'], 404);

        $validated = $request->validate([
            'quantity' => 'integer|min:0',
            'unit_id' => 'exists:units,id'
        ]);

        $inventory->update($validated);
        return response()->json($inventory, 200);
    }

    public function destroy($id) {
        $inventory = InventoryProduct::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Product not found'], 404);

        $inventory->delete();
        return response()->json(['message' => 'Inventory Product deleted successfully'], 200);
    }
}
