<?php

namespace App\Domain\Checkout\Models;

use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutSession extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'session_id',
        'experience_id',
        'experience_option_id',
        'availability_id',
        'locale',
        'currency',
        'booking_type',
        'participants',
        'child_ages',
        'participants_count',
        'charged_seats',
        'tour_language',
        'pickup_status',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'contact_first_name',
        'contact_last_name',
        'contact_email',
        'contact_phone',
        'contact_country',
        'special_requests',
        'price_snapshot',
        'payment_method',
        'payment_status',
        'status',
        'reserved_until',
    ];

    protected function casts(): array
    {
        return [
            'participants' => 'array',
            'child_ages' => 'array',
            'participants_count' => 'integer',
            'charged_seats' => 'integer',
            'pickup_lat' => 'decimal:7',
            'pickup_lng' => 'decimal:7',
            'price_snapshot' => 'array',
            'reserved_until' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ExperienceOption::class, 'experience_option_id');
    }

    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->reserved_until->isFuture();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
