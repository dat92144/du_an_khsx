<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{   protected $table = 'boms';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public function BomItem(){
        return $this->hasOne(BomItem::class,'bom_id');
    }
}
