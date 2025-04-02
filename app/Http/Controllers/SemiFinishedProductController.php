<?php

namespace App\Http\Controllers;

use App\Models\SemiFinishedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SemiFinishedProductController extends Controller {
    public function index() {
        return response()->json(SemiFinishedProduct::all(), 200);
    }

    public function show($id) {
        $semiProduct = SemiFinishedProduct::find($id);
        if (!$semiProduct) return response()->json(['message' => 'Semi-Finished Product not found'], 404);
        return response()->json($semiProduct, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
    
        $latest = DB::table('semi_finished_products')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'SEMI' . str_pad(intval(substr($latest->id, 4)) + 1, 3, '0', STR_PAD_LEFT) : 'SEMI001';
    
        $validated['id'] = $newId;
        $semiProduct = SemiFinishedProduct::create($validated);
    
        return response()->json($semiProduct, 201);
    }    

    public function update(Request $request, $id) {
        $semiProduct = SemiFinishedProduct::find($id);
        if (!$semiProduct) return response()->json(['message' => 'Semi-Finished Product not found'], 404);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string'
        ]);

        $semiProduct->update($validated);
        return response()->json($semiProduct, 200);
    }

    public function destroy($id) {
        $semiProduct = SemiFinishedProduct::find($id);
        if (!$semiProduct) return response()->json(['message' => 'Semi-Finished Product not found'], 404);

        $semiProduct->delete();
        return response()->json(['message' => 'Semi-Finished Product deleted successfully'], 200);
    }
}
