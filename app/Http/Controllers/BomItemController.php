<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BomItem;

class BomItemController extends Controller
{
    public function index($bomId)
    {
        return BomItem::where('bom_id', $bomId)->get();
    }

    public function store(Request $request, $bomId)
    {
        $data = $request->validate([
            'input_material_type' => 'required|string',
            'input_material_id' => 'required|string',
            'quantity_input' => 'required|numeric',
            'input_unit_id' => 'required|string',
            'output_id' => 'required|string',
            'output_type' => 'required|string',
            'output_unit_id' => 'required|string',
        ]);

        $data['bom_id'] = $bomId;

        return BomItem::create($data);
    }

    public function destroy($id)
    {
        $item = BomItem::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Xoá thành phần thành công']);
    }
}
