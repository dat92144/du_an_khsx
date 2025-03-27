<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bom;
class BomController extends Controller
{
    public function index($productId)
    {
        return Bom::where('product_id', $productId)->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|unique:boms',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id'
        ]);

        return Bom::create($data);
    }

    public function update(Request $request, $id)
    {
        $bom = Bom::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $bom->update($data);
        return $bom;
    }

    public function destroy($id)
    {
        $bom = Bom::findOrFail($id);
        $bom->delete();

        return response()->json(['message' => 'Đã xoá BOM']);
    }
}   
