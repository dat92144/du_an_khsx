<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'machines';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'description'];
}
