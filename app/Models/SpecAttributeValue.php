<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecAttributeValue extends Model
{
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
