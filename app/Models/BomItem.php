<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    protected $table = 'bom_items';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'bom_id', 'process_id', 'product_id',
        'input_material_id', 'input_material_type',
        'quantity_input', 'input_unit_id',
        'output_id', 'output_type', 'quantity_output', 'output_unit_id'
    ];
    public function material()
    {
        return $this->belongsTo(Material::class, 'input_material_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
