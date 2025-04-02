<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoutingBom;
use Illuminate\Support\Facades\DB;

class RoutingBomController extends Controller
{
    public function index()
    {
        return response()->json(RoutingBom::with(['product', 'process', 'machine'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'step_order' => 'required|integer',
            'process_id' => 'required|exists:processes,id',
            'machine_id' => 'required|exists:machines,id',
            'cycle_time' => 'required|numeric',
            'lead_time' => 'required|numeric',
        ]);

        $latest = DB::table('routing_bom')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'ROBOM' . str_pad(intval(substr($latest->id, 4)) + 1, 3, '0', STR_PAD_LEFT) : 'MACH001';
    
        $validated['id'] = $newId;

        $routingBom = RoutingBom::create($validated);
        return response()->json($routingBom, 201);
    }

    public function show($id)
    {
        $routingBom = RoutingBom::with(['product', 'process', 'machine'])->findOrFail($id);
        return response()->json($routingBom);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'step_order' => 'sometimes|integer',
            'process_id' => 'sometimes|exists:processes,id',
            'machine_id' => 'sometimes|exists:machines,id',
            'cycle_time' => 'sometimes|numeric',
            'lead_time' => 'sometimes|numeric',
        ]);

        $routingBom = RoutingBom::findOrFail($id);
        $routingBom->update($validated);
        return response()->json($routingBom);
    }

    public function destroy($id)
    {
        RoutingBom::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
