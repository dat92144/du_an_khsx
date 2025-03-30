<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'order_quantity',
        'order_date',
        'delivery_date',
        'bom_id',
        'producing_status'
    ];
    protected $table = 'production_orders';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
}
