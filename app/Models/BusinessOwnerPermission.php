<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOwnerPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 
        'username', 
        'key', 
        'value', 
        'status', 
    ];
}
