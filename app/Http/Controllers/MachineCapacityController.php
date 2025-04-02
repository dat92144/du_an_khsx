<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MachineCapacity;
use Illuminate\Support\Facades\DB;

class MachineCapacityController extends Controller
{
    public function index()
    {
        $capacities = MachineCapacity::join('machines', 'machine_capacity.machine_id', '=', 'machines.id')
            ->select(
                'machine_capacity.id',
                'machines.name as machine_name',
                'machine_capacity.max_output_per_day'
            )
            ->get();
    
        return response()->json($capacities, 200);
    }         

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'max_output_per_day' => 'required|integer|min:1',
        ]);

        $latest = DB::table('machine_capacity')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'MACAP' . str_pad(intval(substr($latest->id, 4)) + 1, 3, '0', STR_PAD_LEFT) : 'MACAP001';
    
        $validated['id'] = $newId;

        $machineCapacity = MachineCapacity::create($validated);
        return response()->json($machineCapacity, 201);
    }

    public function show($id)
    {
        $machineCapacity = MachineCapacity::with('machine')->findOrFail($id);
        return response()->json($machineCapacity);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'machine_id' => 'sometimes|exists:machines,id',
            'max_output_per_day' => 'sometimes|integer|min:1',
        ]);

        $machineCapacity = MachineCapacity::findOrFail($id);
        $machineCapacity->update($validated);
        return response()->json($machineCapacity);
    }

    public function destroy($id)
    {
        MachineCapacity::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
