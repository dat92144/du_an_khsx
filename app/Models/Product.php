<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'description'];
    public $incrementing = false;
    public function BOM(){
        return $this->belongsTo(Bom::class);
    }
    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }
}

