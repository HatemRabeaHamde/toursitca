<?php

namespace App\Domain\Experience\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ExperienceItineraryItem extends Model
{
    use HasTranslations;

    protected $fillable = [
        'experience_id',
        'sort_order',
        'type',
        'title',
        'description',
        'duration_minutes',
        'location_name',
        'location_lat',
        'location_lng',
        'is_main_stop',
        'icon',
    ];

    public array $translatable = ['title', 'description', 'location_name'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'duration_minutes' => 'integer',
            'location_lat' => 'decimal:7',
            'location_lng' => 'decimal:7',
            'is_main_stop' => 'boolean',
        ];
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }
}
