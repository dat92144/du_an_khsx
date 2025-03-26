<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    protected $table = 'bom_items';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public function material()
    {
        return $this->belongsTo(Material::class, 'input_material_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
