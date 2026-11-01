<?php

namespace App\Domain\Review\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/** A photo a traveller attached to their review. */
class ReviewPhoto extends Model
{
    protected $fillable = ['review_id', 'path', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function url(): string
    {
        return Str::startsWith($this->path, ['http://', 'https://', '/']) ? $this->path : asset($this->path);
    }
}
