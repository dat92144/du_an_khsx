<?php

namespace App\Http\Controllers;

use App\Models\MachineSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MachineScheduleController extends Controller {
    public function index()
    {
        $schedules = MachineSchedule::join('machines', 'machine_schedules.machine_id', '=', 'machines.id')
            ->join('production_orders', 'machine_schedules.production_order_id', '=', 'production_orders.id')
            ->select(
                'machine_schedules.id',
                'machines.name as machine_name',
                'production_orders.id as production_order_id',
                'machine_schedules.start_time',
                'machine_schedules.end_time',
                'machine_schedules.status'
            )
            ->get();
    
        return response()->json($schedules, 200);
    }    

    public function show($id) {
        $schedule = MachineSchedule::find($id);
        if (!$schedule) return response()->json(['message' => 'Machine Schedule not found'], 404);
        return response()->json($schedule, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'machine_id' => 'required|string|exists:machines,id',
            'production_order_id' => 'required|string|exists:production_orders,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'status' => 'required|string'
        ]);
    
        $latest = DB::table('machine_schedules')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'MACSCHE' . str_pad(intval(substr($latest->id, 2)) + 1, 3, '0', STR_PAD_LEFT) : 'MS001';
    
        $validated['id'] = $newId;
        $machineSchedule = MachineSchedule::create($validated);
    
        return response()->json($machineSchedule, 201);
    }
    

    public function update(Request $request, $id) {
        $schedule = MachineSchedule::find($id);
        if (!$schedule) return response()->json(['message' => 'Machine Schedule not found'], 404);

        $validated = $request->validate([
            'machine_id' => 'exists:machines,id',
            'production_order_id' => 'exists:production_orders,id',
            'start_time' => 'date',
            'end_time' => 'date|after:start_time',
            'status' => 'string|in:scheduled,in_progress,completed'
        ]);

        $schedule->update($validated);
        return response()->json($schedule, 200);
    }

    public function destroy($id) {
        $schedule = MachineSchedule::find($id);
        if (!$schedule) return response()->json(['message' => 'Machine Schedule not found'], 404);

        $schedule->delete();
        return response()->json(['message' => 'Machine Schedule deleted successfully'], 200);
    }
}
