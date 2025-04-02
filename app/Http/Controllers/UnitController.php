<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller {
    public function index() {
        return response()->json(Unit::all(), 200);
    }

    public function show($id) {
        $unit = Unit::find($id);
        if (!$unit) return response()->json(['message' => 'Unit not found'], 404);
        return response()->json($unit, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
    
        $latest = DB::table('units')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'UNIT' . str_pad(intval(substr($latest->id, 4)) + 1, 3, '0', STR_PAD_LEFT) : 'UNIT001';
    
        $validated['id'] = $newId;
        $unit = Unit::create($validated);
    
        return response()->json($unit, 201);
    }    

    public function update(Request $request, $id) {
        $unit = Unit::find($id);
        if (!$unit) return response()->json(['message' => 'Unit not found'], 404);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string'
        ]);

        $unit->update($validated);
        return response()->json($unit, 200);
    }

    public function destroy($id) {
        $unit = Unit::find($id);
        if (!$unit) return response()->json(['message' => 'Unit not found'], 404);

        $unit->delete();
        return response()->json(['message' => 'Unit deleted successfully'], 200);
    }
}
