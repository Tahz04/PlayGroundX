<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arena extends Model
{
    protected $fillable = [
        'name',
        'type',
        'location',
        'price',
        'latitude',
        'longitude',
        'status',
    ];
}
