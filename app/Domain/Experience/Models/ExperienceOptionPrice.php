<?php

namespace App\Domain\Experience\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceOptionPrice extends Model
{
    protected $fillable = [
        'experience_option_id',
        'participant_type',
        'min_age',
        'max_age',
        'price',
        'original_price',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'min_age' => 'integer',
            'max_age' => 'integer',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
        ];
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ExperienceOption::class, 'experience_option_id');
    }
}
