<?php

namespace App\Domain\Landing\Models;

use App\Domain\Discover\Models\DestinationInterest;
use App\Domain\Discover\Models\GuideBlock;
use App\Domain\Discover\Models\Interest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Destination extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'city',
        'region',
        'summary',
        'tags',
        'image_url',
        'hero_image_url',
        'map_x',
        'map_y',
        'guide',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    public array $translatable = ['name', 'summary', 'tags', 'guide'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'map_x' => 'float',
            'map_y' => 'float',
        ];
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class)
            ->using(DestinationInterest::class)
            ->withPivot(['note', 'sort_order'])
            ->orderByPivot('sort_order');
    }

    public function blocks(): MorphMany
    {
        return $this->morphMany(GuideBlock::class, 'guideable');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /** Destinations with a written guide get their own page. */
    public function scopeWithGuide(Builder $query): Builder
    {
        return $query->whereNotNull('guide');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');
    }
}
