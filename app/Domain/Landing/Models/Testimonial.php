<?php

namespace App\Domain\Landing\Models;

use App\Domain\Discover\Models\Interest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'author_name',
        'author_country',
        'body',
        'experience_title',
        'rating',
        'avatar_url',
        'image_url',
        'city',
        'days',
        'destination_id',
        'interest_id',
        'sort_order',
        'is_active',
    ];

    public array $translatable = ['author_name', 'body'];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'days' => 'integer',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function interest(): BelongsTo
    {
        return $this->belongsTo(Interest::class);
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
