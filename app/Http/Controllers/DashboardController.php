<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        return response()->json([
            'total_orders' => Order::count(), 
            'total_products' => Product::count(), 
            'total_suppliers' => Supplier::count() 
        ]);
    }
}
