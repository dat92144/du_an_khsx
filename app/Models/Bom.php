<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    public function BomItem(){
        return $this->hasMany(BomItem::class,'bom_id');
    }
}
