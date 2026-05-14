<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'message',
        'image_1',
        'image_2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
