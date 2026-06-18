<?php

namespace App\Domain\Experience\Models;

use App\Domain\Booking\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Availability extends Model
{
    protected $fillable = [
        'experience_id',
        'experience_option_id',
        'date',
        'time_slot',
        'max_seats',
        'booked_seats',
        'held_seats',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'max_seats' => 'integer',
            'booked_seats' => 'integer',
            'held_seats' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ExperienceOption::class, 'experience_option_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availableSeats(): int
    {
        return max(0, $this->max_seats - $this->booked_seats - $this->held_seats);
    }
}
