<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    // ProductController.php
    public function index() {
        return Product::all();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'id' => 'required|string|unique:products',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        return Product::create($validated);
    }

    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $product->update($validated);
        return $product;
    }

    public function destroy($id) {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Xoá thành công']);
    }

}
