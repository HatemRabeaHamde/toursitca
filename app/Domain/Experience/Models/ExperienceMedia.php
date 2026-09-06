<?php

namespace App\Domain\Experience\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ExperienceMedia extends Model
{
    protected $table = 'experience_media';

    protected $fillable = [
        'experience_id',
        'type',
        'source_type',
        'path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    public function publicUrl(): string
    {
        if ($this->source_type === 'url' || Str::startsWith($this->path, ['http://', 'https://'])) {
            return $this->path;
        }

        return asset($this->path);
    }
}
