<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UserRole extends Model
{
    protected $table = 'user_roles';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    use HasFactory;
    protected $fillable = [
        'id',
        'user_id',
        'role_id',
    ];
}
