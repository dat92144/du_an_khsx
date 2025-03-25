<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function BOM(){
        return $this->belongsTo(Bom::class);
    }
}

