<?php

namespace App\Domain\Discover\ViewModels;

use App\Domain\Discover\DTOs\HostDirectoryFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/** Local hosts directory ready for Blade. No queries happen here. */
final readonly class HostsPageViewModel
{
    /**
     * @param list<array{label: string, href: ?string}> $breadcrumb
     * @param list<array{label: string, href: string, active: bool}> $regions
     * @param list<array{value: string, label: string}> $specialties
     * @param list<array<string, mixed>> $hosts
     */
    public function __construct(
        public HostDirectoryFilters $filters,
        public array $breadcrumb,
        public array $regions,
        public array $specialties,
        public array $hosts,
        public LengthAwarePaginator $paginator,
        public string $indexUrl,
        public string $becomeHostUrl,
    ) {}

    public function total(): int
    {
        return $this->paginator->total();
    }

    /** @return list<array{value: string, label: string}> */
    public function sorts(): array
    {
        return array_map(
            fn (string $sort): array => ['value' => $sort, 'label' => __('hosts.sort.'.$sort)],
            HostDirectoryFilters::SORTS,
        );
    }

    /** @return list<array{number: int, title: string, body: string}> */
    public function vettingSteps(): array
    {
        return array_map(
            fn (int $step): array => [
                'number' => $step,
                'title' => __("hosts.vetting.steps.{$step}.title"),
                'body' => __("hosts.vetting.steps.{$step}.body"),
            ],
            [1, 2, 3],
        );
    }
}
