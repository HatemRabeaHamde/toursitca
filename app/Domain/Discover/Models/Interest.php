<?php

namespace App\Domain\Discover\Models;

use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/** A way into Morocco by theme (Cuisine, Nature…). Covers one or more experience categories. */
class Interest extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'slug', 'name', 'summary', 'image_url', 'hero_image_url', 'category_slugs', 'guide', 'sort_order', 'is_active',
    ];

    public array $translatable = ['name', 'summary', 'guide'];

    protected function casts(): array
    {
        return [
            'category_slugs' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class)
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
