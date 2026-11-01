<?php

namespace App\DTOs;

final readonly class SiteContentUpdateData
{
    public function __construct(public string $page, public string $mode, public array $fields, public int $userId) {}
}
