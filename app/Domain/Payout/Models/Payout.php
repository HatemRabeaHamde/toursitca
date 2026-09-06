<?php

namespace App\Domain\Payout\Models;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = [
        'agency_id',
        'booking_id',
        'amount',
        'status',
        'notes',
        'transferred_at',
        'transferred_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transferred_at' => 'datetime',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
