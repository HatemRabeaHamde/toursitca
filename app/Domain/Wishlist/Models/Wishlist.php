<?php

namespace App\Domain\Wishlist\Models;

use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Wishlist extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'experience_id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }
}
