<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
class MachineController extends Controller
{
    public function index() {
        return Machine::all();
    }

    public function store(Request $request) {
        $data = $request->validate([
            'id' => 'required|string|max:255',
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:100'
        ]);
        return Machine::create($data);
    }

    public function update(Request $request, $id) {
        $machine = Machine::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:100'
        ]);
        $machine->update($data);
        return $machine;
    }

    public function destroy($id) {
        $machine = Machine::findOrFail($id);
        $machine->delete();
        return response()->json(['message' => 'Xoá thành công']);
    }
}
