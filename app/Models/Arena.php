<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arena extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'owner_id',
        'name',
        'type',
        'location',
        'price',
        'latitude',
        'longitude',
        'status',
        'image',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function isMaintenance()
    {
        return $this->status === self::STATUS_MAINTENANCE;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
