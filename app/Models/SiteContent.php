<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class SiteContent extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $guarded = [];

    // Each image field owns its own SiteContent row and image collection; originals are retained for revision restores.
    public array $translatable = ['value', 'draft_value'];

    public function revisions(): HasMany
    {
        return $this->hasMany(SiteContentRevision::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')->format('webp')->width(1920)->nonQueued();
    }
}
