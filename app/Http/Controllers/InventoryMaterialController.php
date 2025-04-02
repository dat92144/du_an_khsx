<?php

namespace App\Http\Controllers;

use App\Models\InventoryMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InventoryMaterialController extends Controller {
    public function index()
    {
        $materials = InventoryMaterial::join('materials', 'inventory_materials.material_id', '=', 'materials.id')
            ->join('units', 'inventory_materials.unit_id', '=', 'units.id')
            ->select(
                'inventory_materials.id',
                'materials.name as material_name', // Lấy tên nguyên vật liệu
                'inventory_materials.quantity',
                'units.name as unit_name' // Lấy tên đơn vị
            )
            ->get();
    
        return response()->json($materials, 200);
    }    

    public function show($id) {
        $inventory = InventoryMaterial::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Material not found'], 404);
        return response()->json($inventory, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'material_id' => 'required|string|exists:materials,id',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'required|string|exists:units,id'
        ]);
    
        $latest = DB::table('inventory_materials')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'INV' . str_pad(intval(substr($latest->id, 3)) + 1, 3, '0', STR_PAD_LEFT) : 'INV001';
    
        $validated['id'] = $newId;
        $inventoryMaterial = InventoryMaterial::create($validated);
    
        return response()->json($inventoryMaterial, 201);
    }    

    public function update(Request $request, $id) {
        $inventory = InventoryMaterial::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Material not found'], 404);

        $validated = $request->validate([
            'material_id' => 'exists:materials,id',
            'quantity' => 'integer|min:0',
            'unit_id' => 'exists:units,id'
        ]);

        $inventory->update($validated);
        return response()->json($inventory, 200);
    }

    public function destroy($id) {
        $inventory = InventoryMaterial::find($id);
        if (!$inventory) return response()->json(['message' => 'Inventory Material not found'], 404);

        $inventory->delete();
        return response()->json(['message' => 'Inventory Material deleted successfully'], 200);
    }
}
