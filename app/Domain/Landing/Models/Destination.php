<?php

namespace App\Domain\Landing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Destination extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'city',
        'image_url',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    public array $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
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
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');
    }
}
