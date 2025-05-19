<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = [
        'id',
        'product_id',
        'year',
        'total_cost',
        'expected_profit_percent',
        'sell_price',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

