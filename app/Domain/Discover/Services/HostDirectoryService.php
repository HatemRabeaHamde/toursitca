<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\DTOs\HostDirectoryFilters;
use App\Domain\Discover\Models\Host;
use App\Domain\Discover\Models\Interest;
use App\Domain\Discover\ViewModels\HostsPageViewModel;
use App\Domain\Landing\Models\Destination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Local hosts directory: filter by region / specialty / name, sort, paginate.
 * Rating, review count and experience count come from each host's
 * published experiences — nothing is stored on the host itself.
 */
final class HostDirectoryService
{
    public function __construct(private readonly DiscoverUrls $urls) {}

    public function build(HostDirectoryFilters $filters): HostsPageViewModel
    {
        $hosts = $this->paginate($filters);
        $locale = app()->getLocale();

        return new HostsPageViewModel(
            filters: $filters,
            breadcrumb: $this->urls->breadcrumb([['label' => __('hosts.breadcrumb'), 'href' => null]]),
            regions: $this->regions($filters, $locale),
            specialties: $this->specialties($locale),
            hosts: $hosts->getCollection()->map(fn (Host $host): array => $this->card($host, $locale))->all(),
            paginator: $hosts,
            indexUrl: route('site.hosts.index', ['locale' => $locale]),
            becomeHostUrl: route('agency.register'),
        );
    }

    /** Best-rated listed hosts as directory cards (Experiences page · Local partners). @return list<array<string, mixed>> */
    public function top(int $limit = 6): array
    {
        $locale = app()->getLocale();

        return $this->query(new HostDirectoryFilters)->limit($limit)->get()
            ->map(fn (Host $host): array => $this->card($host, $locale))
            ->all();
    }

    private function paginate(HostDirectoryFilters $filters): LengthAwarePaginator
    {
        return $this->query($filters)->paginate((int) config('discover.hosts.per_page', 12))->withQueryString();
    }

    private function query(HostDirectoryFilters $filters): Builder
    {
        $published = fn (Builder $query) => $query->published();

        $query = Host::query()
            ->listed()
            ->withCount(['experiences as experiences_total' => $published])
            ->withSum(['experiences as reviews_total' => $published], 'reviews_count')
            ->withAvg(['experiences as rating_value' => fn (Builder $query) => $query->published()->where('reviews_count', '>', 0)], 'rating_avg')
            ->when($filters->region, fn (Builder $query, string $slug) => $query->whereHas('destination', fn (Builder $query) => $query->where('slug', $slug)))
            ->when($filters->specialty, fn (Builder $query, string $slug) => $query->whereHas('interest', fn (Builder $query) => $query->where('slug', $slug)))
            ->when($filters->search, fn (Builder $query, string $search) => $this->search($query, $search));

        match ($filters->sort) {
            'experiences' => $query->orderByDesc('experiences_total')->orderBy('name'),
            'name' => $query->orderBy('name'),
            default => $query->orderByRaw('rating_value IS NULL')->orderByDesc('rating_value')->orderByDesc('reviews_total')->orderBy('sort_order'),
        };

        return $query;
    }

    /** Name, role (any language) or city. */
    private function search(Builder $query, string $search): void
    {
        $like = '%'.mb_strtolower(addcslashes($search, '%_\\')).'%';

        $query->where(fn (Builder $query) => $query
            ->whereRaw('LOWER(name) LIKE ?', [$like])
            ->orWhereRaw('LOWER(role) LIKE ?', [$like])
            ->orWhereRaw('LOWER(city) LIKE ?', [$like]));
    }

    /** "All" + every region that has at least one listed host. @return list<array{label: string, href: string, active: bool}> */
    private function regions(HostDirectoryFilters $filters, string $locale): array
    {
        $index = route('site.hosts.index', ['locale' => $locale]);
        $chip = fn (?string $slug, string $label): array => [
            'label' => $label,
            'href' => $index.$this->queryString($filters->query(['region' => $slug])).'#directory',
            'active' => $filters->region === $slug,
        ];

        return [
            $chip(null, __('hosts.filters.all')),
            ...Destination::query()->active()
                ->whereIn('id', Host::query()->listed()->whereNotNull('destination_id')->select('destination_id'))
                ->orderBy('sort_order')
                ->get(['id', 'slug', 'name'])
                ->map(fn (Destination $region): array => $chip($region->slug, $region->getTranslation('name', $locale, true)))
                ->all(),
        ];
    }

    /** Specialties that at least one listed host has. @return list<array{value: string, label: string}> */
    private function specialties(string $locale): array
    {
        return Interest::query()->active()->sorted()
            ->whereIn('id', Host::query()->listed()->whereNotNull('interest_id')->select('interest_id'))
            ->get(['id', 'slug', 'name'])
            ->map(fn (Interest $interest): array => ['value' => $interest->slug, 'label' => $interest->getTranslation('name', $locale, true)])
            ->all();
    }

    /** @return array<string, mixed> */
    private function card(Host $host, string $locale): array
    {
        $reviews = (int) $host->reviews_total;

        return [
            'href' => $this->urls->host($host->slug),
            'name' => $host->name,
            'role' => trim($host->getTranslation('role', $locale, true).($host->city ? ' · '.$host->city : '')),
            'image' => (string) $host->photo_url,
            'avatar' => (string) ($host->avatar_url ?: $host->photo_url),
            'rating' => $host->rating_value !== null && $reviews > 0 ? number_format((float) $host->rating_value, 1) : null,
            'reviews' => $reviews ?: null,
            'licence' => $host->verifiedLicence(),
            'experiences' => (int) $host->experiences_total,
        ];
    }

    private function queryString(array $query): string
    {
        return $query === [] ? '' : '?'.http_build_query($query);
    }
}
