<?php

namespace App\Domain\Discover\DTOs;

/** What the visitor asked the Local hosts directory for. */
final readonly class HostDirectoryFilters
{
    public const SORTS = ['top_rated', 'experiences', 'name'];

    public function __construct(
        public ?string $region = null,
        public ?string $specialty = null,
        public ?string $search = null,
        public string $sort = 'top_rated',
    ) {}

    /** Query string for these filters, with optional overrides (null removes a key). */
    public function query(array $overrides = []): array
    {
        return array_filter([
            'region' => $this->region,
            'specialty' => $this->specialty,
            'q' => $this->search,
            'sort' => $this->sort === 'top_rated' ? null : $this->sort,
            ...$overrides,
        ], fn ($value): bool => $value !== null && $value !== '');
    }

    public function isFiltered(): bool
    {
        return $this->region !== null || $this->specialty !== null || $this->search !== null;
    }
}
