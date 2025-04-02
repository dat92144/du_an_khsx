<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionPlan extends Model {
    use HasFactory;

    protected $primaryKey = 'plan_id';
    public $incrementing = false;
    protected $table = 'production_plans';
    protected $fillable = [
        'plan_id', 'order_id', 'product_id', 'lot_number', 'lot_size',
        'total_quantity', 'machine_id', 'process_id',
        'start_time', 'end_time', 'delivery_date', 'status'
    ];
        public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }
    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'order_id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'order_id');
    }
}
