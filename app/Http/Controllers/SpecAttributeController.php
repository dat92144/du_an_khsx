<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpecAttribute;
class SpecAttributeController extends Controller
{
    public function index()
    {
        return SpecAttribute::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'=> 'required|string|max:255',
            'spec_id' => 'required|exists:specs,id',
            'name' => 'required|string|max:255',
            'attribute_type' => 'required|in:number,text,boolean',
        ]);
        return SpecAttribute::create($validated);
    }

    public function update(Request $request, $id)
    {
        $specAttribute = SpecAttribute::findOrFail($id);
        $validated = $request->validate([
            'id' => 'required|string|max:255',
            'spec_id' => 'required|exists:specs,id',
            'name' => 'required|string|max:255',
            'attribute_type' => 'required|in:number,text,boolean',
        ]);
        $specAttribute->update($validated);
        return $specAttribute;
    }

    public function destroy($id)
    {
        $specAttribute = SpecAttribute::findOrFail($id);
        $specAttribute->delete();
        return response()->json(['message'=>'đã xoá']);
    }
}
