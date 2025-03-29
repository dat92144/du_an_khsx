<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecAttributeValue extends Model
{
    protected $table = 'spec_attribute_values';
    protected $fillable =[
        'spec_attribute_id',
        'number_value',
        'text_value',
        'boolean_value'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function spec()
    {
        return $this->belongsTo(Spec::class);
    }
    public function specAttribute()
    {
        return $this->belongsTo(SpecAttribute::class);
    }
}
