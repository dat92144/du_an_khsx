<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model {
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'semi_finished_product_id',
        'order_quantity',
        'order_date',
        'delivery_date',
        'bom_id',
        'producing_status',
        'created_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function machineSchedules() {
        return $this->hasMany(MachineSchedule::class);
    }
    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
