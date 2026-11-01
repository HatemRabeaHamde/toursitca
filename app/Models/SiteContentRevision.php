<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContentRevision extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'array', 'created_at' => 'datetime'];
    }
}
