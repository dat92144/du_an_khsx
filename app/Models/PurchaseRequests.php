<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequests extends Model
{
    protected $table = 'purchase_requests';
    protected $fillable = [
        'supplier_id', 
        'material_id', 
        'type', 
        'quantity', 
        'unit_id', 
        'price_per_unit', 
        'total_price',  
        'expected_delivery_date', 
        'status'
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
