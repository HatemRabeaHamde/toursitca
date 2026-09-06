<?php

namespace App\Domain\Experience\Models;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Review\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Experience extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'slug',
        'title',
        'description',
        'category',
        'difficulty',
        'duration_hours',
        'max_group_size',
        'price_per_person',
        'original_price',
        'deal_starts_at',
        'deal_ends_at',
        'private_price',
        'pickup_enabled',
        'location_city',
        'location_lat',
        'location_lng',
        'meeting_point',
        'inclusions',
        'exclusions',
        'status',
        'thumbnail',
        'rating_avg',
        'reviews_count',
        'is_top_rated',
        'created_by',
    ];

    public array $translatable = ['title', 'description', 'inclusions', 'exclusions'];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'decimal:1',
            'price_per_person' => 'decimal:2',
            'original_price' => 'decimal:2',
            'deal_starts_at' => 'datetime',
            'deal_ends_at' => 'datetime',
            'private_price' => 'decimal:2',
            'pickup_enabled' => 'boolean',
            'location_lat' => 'decimal:7',
            'location_lng' => 'decimal:7',
            'rating_avg' => 'decimal:2',
            'reviews_count' => 'integer',
            'is_top_rated' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Experience $experience): void {
            if (filled($experience->slug)) {
                return;
            }

            $title = $experience->getTranslations('title');
            $base = (string) collect($title)->filter()->first();
            $slug = Str::slug($base) ?: 'experience';

            $experience->slug = static::uniqueSlug($slug, $experience->exists ? $experience->id : null);
        });
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ExperienceMedia::class)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->media()->where('type', 'image');
    }

    public function videos(): HasMany
    {
        return $this->media()->where('type', 'video');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ExperienceOption::class);
    }

    public function itineraryItems(): HasMany
    {
        return $this->hasMany(ExperienceItineraryItem::class)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_visible', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function supportsPrivateBooking(): bool
    {
        return $this->private_price !== null;
    }

    private static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, fn (Builder $query): Builder => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
