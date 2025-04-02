<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller {
    public function index() {
        return response()->json(Material::all(), 200);
    }

    public function show($id) {
        $material = Material::find($id);
        if (!$material) return response()->json(['message' => 'Material not found'], 404);
        return response()->json($material, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
    
        $latest = DB::table('materials')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'mat' . str_pad(intval(substr($latest->id, 3)) + 1, 3, '0', STR_PAD_LEFT) : 'mat001';
    
        $validated['id'] = $newId;
        $material = Material::create($validated);
    
        return response()->json($material, 201);
    }
    

    public function update(Request $request, $id) {
        $material = Material::find($id);
        if (!$material) return response()->json(['message' => 'Material not found'], 404);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string'
        ]);

        $material->update($validated);
        return response()->json($material, 200);
    }

    public function destroy($id) {
        $material = Material::find($id);
        if (!$material) return response()->json(['message' => 'Material not found'], 404);

        $material->delete();
        return response()->json(['message' => 'Material deleted successfully'], 200);
    }
}
