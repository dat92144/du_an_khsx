<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpecAttributeValue;

class SpecAttributeValueController extends Controller
{
    public function index()
    {
        return SpecAttributeValue::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'spec_attribute_id' => 'required|exists:spec_attributes,id',
            'number_value' => 'nullable|numeric',
            'text_value' => 'nullable|string',
            'boolean_value' => 'nullable|boolean',
        ]);
        return SpecAttributeValue::create($validated);
    }

    public function update(Request $request, $id)
    {
        $specAttributeValue = SpecAttributeValue::findOrFail($id);
        $validated = $request->validate([
            'spec_attribute_id' => 'required|exists:spec_attributes,id',
            'number_value' => 'nullable|numeric',
            'text_value' => 'nullable|string',
            'boolean_value' => 'nullable|boolean',
        ]);
        $specAttributeValue->update($validated);
        return $specAttributeValue;
    }

    public function destroy($id)
    {
        $specAttributeValue = SpecAttributeValue::findOrFail($id);
        $specAttributeValue->delete();
        return response()->noContent();
    }
}
