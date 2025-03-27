<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spec;

class SpecController extends Controller
{
    public function index()
    {
        return Spec::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|unique:specs,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'process_id' => 'required|exists:processes,id',
            'machine_id' => 'required|exists:machines,id',
            'lead_time' => 'required|numeric',
            'cycle_time' => 'required|numeric',
            'lot_size' => 'required|integer',
        ]);
        return Spec::create($validated);
    }

    public function update(Request $request, $id)
    {
        $spec = Spec::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'process_id' => 'required|exists:processes,id',
            'machine_id' => 'required|exists:machines,id',
            'lead_time' => 'required|numeric',
            'cycle_time' => 'required|numeric',
            'lot_size' => 'required|integer',
        ]);
        $spec->update($validated);
        return $spec;
    }

    public function destroy($id)
    {
        $spec = Spec::findOrFail($id);
        $spec->delete();
        return response()->json(['message' => 'Đã xoá']);
    }
}
