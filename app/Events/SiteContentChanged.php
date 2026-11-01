<?php

namespace App\Events;

final readonly class SiteContentChanged
{
    public function __construct(public string $page, public int $userId, public string $mode) {}
}
