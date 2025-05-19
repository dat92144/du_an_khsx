<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCost extends Model
{
    protected $fillable = [
        'id',
        'product_id',
        'material_cost',
        'overhead_cost',
        'inventory_cost',
        'transportation_cost',
        'wastage_cost',
        'depreciation_cost',
        'service_outsourcing_cost',
        'other_costs',
        'total_cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}