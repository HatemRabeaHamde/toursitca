<?php

namespace App\Domain\Review\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'user_id',
        'experience_id',
        'rating',
        'rating_host',
        'rating_organisation',
        'rating_pickup',
        'rating_value',
        'body',
        'is_visible',
        'agency_reply',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'rating_host' => 'integer',
            'rating_organisation' => 'integer',
            'rating_pickup' => 'integer',
            'rating_value' => 'integer',
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

    public function photos(): HasMany
    {
        return $this->hasMany(ReviewPhoto::class)->orderBy('sort_order');
    }

    /** Detail ratings shown on the reviews page (column => lang key). */
    public const DETAIL_RATINGS = [
        'rating_host' => 'hosts',
        'rating_organisation' => 'organisation',
        'rating_pickup' => 'pickup',
        'rating_value' => 'value',
    ];
}
