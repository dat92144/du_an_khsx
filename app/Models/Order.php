<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;

class Order extends Model {
    use HasFactory;
    protected $table = 'orders';    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'customer_id', 'order_date', 'delivery_date', 'status'];

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }    
   
        public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }
}