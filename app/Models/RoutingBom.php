<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutingBom extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'routing_bom';
    protected $fillable = [
        'id','product_id', 'step_order', 'process_id', 'machine_id', 'cycle_time', 'lead_time'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
