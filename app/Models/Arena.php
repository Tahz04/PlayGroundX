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
        'maintenance_note',
        'image',
        'image_1',
        'image_2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating(): ?float
    {
        $rating = $this->reviews_avg_rating ?? $this->reviews()->avg('rating');

        return $rating === null ? null : round($rating, 1);
    }

    public function reviewsCount(): int
    {
        return $this->reviews_count ?? $this->reviews()->count();
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
