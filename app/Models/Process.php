<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $table = 'processes';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'description'];
}
