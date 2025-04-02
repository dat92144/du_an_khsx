<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller {
    public function index() {
        return response()->json(Customer::all(), 200);
    }

    public function show($id) {
        $customer = Customer::find($id);
        if (!$customer) return response()->json(['message' => 'Customer not found'], 404);
        return response()->json($customer, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string'
        ]);
    
        $latest = DB::table('customers')->orderBy('id', 'desc')->first();
        $newId = $latest ? 'cus' . str_pad(intval(substr($latest->id, 3)) + 1, 3, '0', STR_PAD_LEFT) : 'cus001';
    
        $validated['id'] = $newId;
        $customer = Customer::create($validated);
    
        return response()->json($customer, 201);
    }    

    public function update(Request $request, $id) {
        $customer = Customer::find($id);
        if (!$customer) return response()->json(['message' => 'Customer not found'], 404);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string'
        ]);

        $customer->update($validated);
        return response()->json($customer, 200);
    }

    public function destroy($id) {
        $customer = Customer::find($id);
        if (!$customer) return response()->json(['message' => 'Customer not found'], 404);

        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully'], 200);
    }
}
