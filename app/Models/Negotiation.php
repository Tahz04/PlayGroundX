<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Negotiation extends Model
{
    protected $fillable = [
        'booking_id',
        'arena_id',
        'user_id',
        'proposed_price',
        'message',
        'status',
        'owner_note',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function arena(): BelongsTo
    {
        return $this->belongsTo(Arena::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
