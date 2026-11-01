<?php

namespace App\Domain\Discover\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Translatable\HasTranslations;

/** Destination ↔ Interest link with a short, translatable note. */
class DestinationInterest extends Pivot
{
    use HasTranslations;

    protected $table = 'destination_interest';

    public $incrementing = true;

    public array $translatable = ['note'];
}
