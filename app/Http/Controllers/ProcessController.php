<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Process;
class ProcessController extends Controller
{
    public function index() {
        return Process::all();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'id' => 'required|string|unique:processes,id',
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);
        return Process::create($validated);
    }

    public function update(Request $request, $id) {
        $process = Process::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);
        $process->update($validated);
        return $process;
    }

    public function destroy($id) {
        Process::findOrFail($id)->delete();
        return response()->json(['message' => 'Xoá thành công']);
    }
}
