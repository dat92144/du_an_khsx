<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'id',
        'name',
        'contact_info',
        'created_at',
        'updated_at'
    ];
    protected $table = 'suppliers';
    protected $primaryKey = 'id'; // Đảm bảo Laravel nhận diện đúng cột id
    protected $keyType = 'string';
}
