<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public function bomItems()
    {
        return $this->hasMany(BomItem::class, 'input_material_id');
    }
}
