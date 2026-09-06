<?php

namespace App\Domain\Review\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'user_id',
        'experience_id',
        'rating',
        'body',
        'is_visible',
        'agency_reply',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_visible' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }
}
