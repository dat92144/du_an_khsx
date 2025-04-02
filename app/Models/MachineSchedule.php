<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineSchedule extends Model {
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'machine_id', 'production_order_id', 'start_time', 'end_time', 'status'];

    public function machine(): BelongsTo {
        return $this->belongsTo(Machine::class);
    }

    public function productionOrder(): BelongsTo {
        return $this->belongsTo(ProductionOrder::class);
    }
}