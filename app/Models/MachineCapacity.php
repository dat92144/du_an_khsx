<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineCapacity extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'machine_capacity';
    protected $fillable = ['id','machine_id', 'max_output_per_day'];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
