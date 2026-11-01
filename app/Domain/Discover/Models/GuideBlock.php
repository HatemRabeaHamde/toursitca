<?php

namespace App\Domain\Discover\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

/**
 * One repeatable piece of a guide: a landmark, dish, tip, origin story,
 * craft photo or "also look for" note. Belongs to a Destination or Interest.
 */
class GuideBlock extends Model
{
    use HasTranslations;

    public const LANDMARK = 'landmark';
    public const DISH = 'dish';
    public const TIP = 'tip';
    public const ORIGIN = 'origin';
    public const CRAFT = 'craft';
    public const ALSO = 'also';

    protected $fillable = ['type', 'title', 'body', 'image_url', 'sort_order', 'is_active'];

    public array $translatable = ['title', 'body'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function guideable(): MorphTo
    {
        return $this->morphTo();
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
