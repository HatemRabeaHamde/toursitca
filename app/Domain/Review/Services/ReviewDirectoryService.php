<?php

namespace App\Domain\Review\Services;

use App\Domain\Discover\Models\Host;
use App\Domain\Discover\Models\Interest;
use App\Domain\Experience\Models\Experience;
use App\Domain\Review\DTOs\ReviewDirectoryFilters;
use App\Domain\Review\Models\Review;
use App\Domain\Review\ViewModels\ReviewsPageViewModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Traveller reviews: only visible reviews tied to a booking. Summary numbers
 * (average, star breakdown, detail ratings) are computed from those reviews.
 */
final class ReviewDirectoryService
{
    public function build(ReviewDirectoryFilters $filters): ReviewsPageViewModel
    {
        return $this->make($filters, withSummary: true);
    }

    /** Only what changes when filtering in place. */
    public function results(ReviewDirectoryFilters $filters): ReviewsPageViewModel
    {
        return $this->make($filters, withSummary: false);
    }

    private function make(ReviewDirectoryFilters $filters, bool $withSummary): ReviewsPageViewModel
    {
        $locale = app()->getLocale();
        $page = $this->paginate($filters);
        $index = route('site.reviews.index', ['locale' => $locale]);

        return new ReviewsPageViewModel(
            filters: $filters,
            chips: $this->chips($filters, $locale, $index),
            reviews: $page->getCollection()->map(fn (Review $review): array => $this->card($review, $locale))->all(),
            paginator: $page,
            summary: $withSummary ? $this->summary() : null,
            experiences: $withSummary ? $this->experienceOptions($locale) : [],
            hosts: $withSummary ? $this->hostOptions() : [],
            indexUrl: $index,
            experiencesUrl: route('site.experiences.index', ['locale' => $locale]),
        );
    }

    private function visible(): Builder
    {
        return Review::query()->where('is_visible', true)->whereNotNull('booking_id');
    }

    private function paginate(ReviewDirectoryFilters $filters): LengthAwarePaginator
    {
        $query = $this->visible()
            ->with(['user:id,name,avatar', 'booking:id,contact_country,availability_id', 'booking.availability:id,date', 'experience:id,slug,title,host_id', 'experience.host:id,name', 'photos'])
            ->when($filters->interest, function (Builder $query, string $slug): void {
                $categories = (array) Interest::query()->where('slug', $slug)->value('category_slugs');
                $query->whereHas('experience', fn (Builder $query) => $query->whereIn('category', $categories ?: ['__none__']));
            })
            ->when($filters->experience, fn (Builder $query, string $slug) => $query->whereHas('experience', fn (Builder $query) => $query->where('slug', $slug)))
            ->when($filters->host, fn (Builder $query, string $slug) => $query->whereHas('experience.host', fn (Builder $query) => $query->where('slug', $slug)))
            ->when($filters->rating, fn (Builder $query, int $rating) => $rating === 5 ? $query->where('rating', 5) : $query->where('rating', '>=', $rating))
            ->when($filters->withPhotos, fn (Builder $query) => $query->has('photos'));

        match ($filters->sort) {
            'highest' => $query->orderByDesc('rating')->latest(),
            'lowest' => $query->orderBy('rating')->latest(),
            default => $query->latest(),
        };

        return $query->paginate((int) config('experiences.reviews.per_page', 6))->withQueryString();
    }

    /** @return array{average: string, count: int, breakdown: list<array{stars: int, percent: int}>, details: list<array{key: string, value: string}>}|null */
    private function summary(): ?array
    {
        $count = $this->visible()->count();
        if ($count === 0) {
            return null;
        }

        $byStars = $this->visible()->selectRaw('rating, COUNT(*) as total')->groupBy('rating')->pluck('total', 'rating');
        $details = $this->visible()
            ->toBase()
            ->selectRaw(collect(Review::DETAIL_RATINGS)->keys()->map(fn (string $column): string => "AVG({$column}) as avg_{$column}")->implode(', '))
            ->first();

        return [
            'average' => number_format((float) $this->visible()->avg('rating'), 1),
            'count' => $count,
            'breakdown' => collect([5, 4, 3, 2, 1])
                ->map(fn (int $stars): array => ['stars' => $stars, 'percent' => (int) round(((int) ($byStars[$stars] ?? 0)) / $count * 100)])
                ->all(),
            'details' => collect(Review::DETAIL_RATINGS)
                ->filter(fn (string $key, string $column): bool => $details?->{'avg_'.$column} !== null)
                ->map(fn (string $key, string $column): array => ['key' => $key, 'value' => number_format((float) $details->{'avg_'.$column}, 1)])
                ->values()
                ->all(),
        ];
    }

    /** "All" + every interest. */
    private function chips(ReviewDirectoryFilters $filters, string $locale, string $index): array
    {
        $chip = fn (?string $slug, string $label): array => [
            'label' => $label,
            'href' => $index.$this->qs($filters->query(['interest' => $slug, 'page' => null])).'#reviews',
            'active' => $filters->interest === $slug,
        ];

        return [
            $chip(null, __('reviews.filters.all')),
            ...Interest::query()->active()->sorted()->get(['id', 'slug', 'name'])
                ->map(fn (Interest $interest): array => $chip($interest->slug, $interest->getTranslation('name', $locale, true)))
                ->all(),
        ];
    }

    /** @return list<array{value: string, label: string}> experiences that have reviews */
    private function experienceOptions(string $locale): array
    {
        return Experience::query()
            ->whereIn('id', $this->visible()->select('experience_id'))
            ->get(['id', 'slug', 'title'])
            ->map(fn (Experience $experience): array => ['value' => (string) $experience->slug, 'label' => $experience->getTranslation('title', $locale, true)])
            ->sortBy('label')
            ->values()
            ->all();
    }

    /** @return list<array{value: string, label: string}> hosts whose experiences have reviews */
    private function hostOptions(): array
    {
        return Host::query()
            ->whereIn('id', Experience::query()->whereIn('id', $this->visible()->select('experience_id'))->whereNotNull('host_id')->select('host_id'))
            ->orderBy('name')
            ->get(['id', 'slug', 'name'])
            ->map(fn (Host $host): array => ['value' => $host->slug, 'label' => $host->name])
            ->all();
    }

    /** @return array<string, mixed> */
    private function card(Review $review, string $locale): array
    {
        $name = $review->user?->name ? Str::before(trim($review->user->name), ' ') : __('reviews.anonymous');
        $host = $review->experience?->host?->name;
        $travelled = $review->booking?->availability?->date ?? $review->created_at;

        return [
            'author' => collect([$name, $review->booking?->contact_country])->filter()->implode(', '),
            'initials' => Str::upper(Str::substr($name, 0, 1)),
            'avatar' => $this->url($review->user?->avatar),
            'travelled' => $travelled ? __('reviews.card.travelled', ['date' => $travelled->locale($locale)->translatedFormat('F Y')]) : null,
            'rating' => (int) $review->rating,
            'body' => trim((string) $review->body),
            'photos' => $review->photos->map->url()->all(),
            'trip' => $review->experience ? collect([
                $review->experience->getTranslation('title', $locale, true),
                $host ? __('experiences.card.with', ['name' => $host]) : null,
            ])->filter()->implode(' · ') : null,
            'tripUrl' => $review->experience?->slug ? route('site.experiences.show', ['locale' => $locale, 'experience' => $review->experience->slug]) : null,
            'reply' => $review->agency_reply ? ['from' => $host, 'body' => $review->agency_reply] : null,
        ];
    }

    private function url(?string $path): ?string
    {
        return $path ? (Str::startsWith($path, ['http://', 'https://', '/']) ? $path : asset($path)) : null;
    }

    private function qs(array $query): string
    {
        $query = array_filter($query, fn ($value): bool => $value !== null);

        return $query === [] ? '' : '?'.http_build_query($query);
    }
}
