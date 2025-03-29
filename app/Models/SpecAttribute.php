<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecAttribute extends Model
{
    protected $table = 'spec_attributes';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'spec_id', 'name', 'attribute_type'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function spec()
    {
        return $this->belongsTo(Spec::class);
    }
    public function SpecAttributeValues()
    {
        return $this->hasMany(SpecAttributeValue::class);
    }
}
