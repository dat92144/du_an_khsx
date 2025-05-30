<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;
class ProductionPlan extends Model {
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $table = 'production_plans';
    protected $fillable = [
        'id', 'order_id', 'product_id','semi_finished_product_id', 'lot_number', 'lot_size',
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

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id', 'id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

}
