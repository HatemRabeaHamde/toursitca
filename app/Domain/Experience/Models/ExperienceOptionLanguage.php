<?php

namespace App\Domain\Experience\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceOptionLanguage extends Model
{
    protected $fillable = [
        'experience_option_id',
        'language_code',
        'type',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ExperienceOption::class, 'experience_option_id');
    }
}
