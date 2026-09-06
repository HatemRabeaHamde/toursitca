<?php

namespace App\Domain\Agency\Models;

use App\Domain\Experience\Models\Experience;
use App\Domain\Payout\Models\Payout;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Agency extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'commission_rate',
        'status',
        'city',
        'phone',
        'languages',
        'is_platform',
    ];

    public array $translatable = ['description'];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'languages' => 'array',
            'is_platform' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function scopePlatform(Builder $query): Builder
    {
        return $query->where('is_platform', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
