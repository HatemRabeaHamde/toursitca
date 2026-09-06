<?php

namespace App\Domain\Experience\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ExperienceOption extends Model
{
    use HasTranslations;

    protected $fillable = [
        'experience_id',
        'title',
        'duration_minutes',
        'pickup_enabled',
        'private_available',
        'pay_later_enabled',
        'cancellation_hours',
        'price_type',
        'status',
    ];

    public array $translatable = ['title'];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'pickup_enabled' => 'boolean',
            'private_available' => 'boolean',
            'pay_later_enabled' => 'boolean',
            'cancellation_hours' => 'integer',
        ];
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ExperienceOptionPrice::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(ExperienceOptionLanguage::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }
}
