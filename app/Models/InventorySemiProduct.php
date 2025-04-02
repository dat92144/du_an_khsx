<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySemiProduct extends Model {
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'semi_product_id', 'quantity', 'unit_id'];

    public function semiProduct(): BelongsTo {
        return $this->belongsTo(SemiFinishedProduct::class, 'semi_product_id');
    }

    public function unit(): BelongsTo {
        return $this->belongsTo(Unit::class);
    }
}
