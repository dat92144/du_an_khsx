<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCostHistory extends Model
{
    protected $fillable = [
        'product_id',
        'year',
        'old_total_cost',
        'total_cost',
        'reason'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

