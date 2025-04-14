<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemiFinishedProduct extends Model
{
    protected $table = 'semi_finished_products';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'description'];
}
