<?php

namespace App\Domain\Discover\Models;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Experience;
use App\Domain\Landing\Models\Destination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/** A named local person who leads experiences (Fatima, Hassan…). */
class Host extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'agency_id', 'destination_id', 'interest_id', 'slug', 'name', 'role', 'city', 'bio', 'photo_url', 'avatar_url',
        'licence_number', 'is_verified', 'sort_order', 'is_active',
        'quote', 'story', 'skills', 'languages', 'partner_since', 'cover_url', 'reviewed_at',
    ];

    public array $translatable = ['role', 'bio', 'quote', 'story', 'skills'];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'languages' => 'array',
            'partner_since' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /** The region the host works in (Marrakech, Sahara, Atlantic coast…). */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /** The host's main specialty. */
    public function interest(): BelongsTo
    {
        return $this->belongsTo(Interest::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    /** A licence is only shown publicly once an admin has verified the host. */
    public function verifiedLicence(): ?string
    {
        return $this->is_verified ? ($this->licence_number ?: null) : null;
    }

    /** New profiles and changed licences wait here for an admin. */
    public function scopeAwaitingReview(Builder $query): Builder
    {
        return $query->whereNull('reviewed_at');
    }

    public function scopeOfAgency(Builder $query, int $agencyId): Builder
    {
        return $query->where('agency_id', $agencyId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Hosts the directory may show: active and with a portrait. */
    public function scopeListed(Builder $query): Builder
    {
        return $query->active()->whereNotNull('photo_url');
    }

    public function scopeInCity(Builder $query, string $city): Builder
    {
        return $query->whereRaw('LOWER(city) = ?', [mb_strtolower($city)]);
    }
}
