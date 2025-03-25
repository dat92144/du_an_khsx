<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPrice extends Model
{
    protected $table = 'supplier_prices';
    protected $primaryKey = 'id'; // Đảm bảo Laravel nhận diện đúng cột id
    protected $keyType = 'string';
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

}
