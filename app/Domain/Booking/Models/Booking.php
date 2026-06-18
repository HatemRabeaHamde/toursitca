<?php

namespace App\Domain\Booking\Models;

use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use App\Domain\Payout\Models\Payout;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'checkout_session_id',
        'experience_id',
        'experience_option_id',
        'availability_id',
        'booking_type',
        'participants_count',
        'participants',
        'charged_seats',
        'tour_language',
        'pickup_status',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'unit_price',
        'total_price',
        'commission_rate',
        'commission_amount',
        'agency_amount',
        'price_snapshot',
        'payment_method',
        'payment_status',
        'status',
        'guest_name',
        'guest_email',
        'guest_phone',
        'contact_country',
        'special_notes',
        'cancelled_at',
        'cancellation_reason',
        'reserved_until',
    ];

    protected function casts(): array
    {
        return [
            'participants_count' => 'integer',
            'participants' => 'array',
            'charged_seats' => 'integer',
            'pickup_lat' => 'decimal:7',
            'pickup_lng' => 'decimal:7',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'agency_amount' => 'decimal:2',
            'price_snapshot' => 'array',
            'cancelled_at' => 'datetime',
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

    public function checkoutSession(): BelongsTo
    {
        return $this->belongsTo(CheckoutSession::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ExperienceOption::class, 'experience_option_id');
    }

    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }

    public function payout(): HasOne
    {
        return $this->hasOne(Payout::class);
    }
}
