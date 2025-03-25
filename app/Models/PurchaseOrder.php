<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id', 
        'material_id', 
        'type', 
        'quantity', 
        'unit_id', 
        'price_per_unit', 
        'total_price', 
        'order_date', 
        'expected_delivery_date', 
        'status'
    ];
}
