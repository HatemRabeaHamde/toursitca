<?php

namespace App\ViewModels;

use Illuminate\Support\Arr;

final readonly class SiteContentPage
{
    public function __construct(public string $page, public array $values, public bool $preview = false, public array $types = []) {}

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? '';
    }

    public function group(string $key): array
    {
        return Arr::get(Arr::undot($this->values), $key, []);
    }
}
