<?php

namespace App\Domain\Landing\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'message',
        'locale',
        'source',
        'subscribed_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }
}
