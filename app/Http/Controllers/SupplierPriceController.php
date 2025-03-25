<?php

namespace App\Http\Controllers;

use App\Models\SupplierPrice;
use Illuminate\Http\Request;

class SupplierPriceController extends Controller
{
    public function index($supplier_id)
{
    $prices = SupplierPrice::where('supplier_id', $supplier_id)->with('unit','material')->get();
    return response()->json($prices->map(function ($price) {
        return [
            'id' => $price->id,
            'supplier_id' => $price->supplier_id,
            'material' => $price->material->name ?? null,
            'price_per_unit' => $price->price_per_unit,
            'unit' => $price->unit->name ?? null ,
            'effective_date' => $price->effective_date,
            'delivery_time' => $price->delivery_time
        ];
    }));
}

}
