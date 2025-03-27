<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spec extends Model
{
    protected $table = 'specs';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'name', 'description',
        'product_id', 'process_id', 'machine_id',
        'lead_time', 'cycle_time', 'lot_size'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function SpecAttributes()
    {
        return $this->hasMany(SpecAttribute::class);
    }
    public function SpecAttributeValues()
    {
        return $this->hasMany(SpecAttributeValue::class);
    }
}
