<?php

namespace App\Domain\Landing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        ];
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
