<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryProduct extends Model {
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'product_id', 'quantity', 'unit_id'];

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo {
        return $this->belongsTo(Unit::class);
    }
}
