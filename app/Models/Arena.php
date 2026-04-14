<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arena extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'type',
        'location',
        'price',
        'latitude',
        'longitude',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
