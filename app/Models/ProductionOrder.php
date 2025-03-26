<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $table = 'production_orders';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
}
