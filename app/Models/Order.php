<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'customer_id', 'order_date', 'delivery_date', 'status'
    ];

    public $incrementing = false;

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
