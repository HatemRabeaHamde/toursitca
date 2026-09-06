<?php

namespace App\Domain\Experience\Queries;

use App\Domain\Experience\DTOs\ExperienceSearchData;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceCardPresenter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class ExperienceSearchQuery
{
    public function paginate(ExperienceSearchData $data, int $perPage = 12): LengthAwarePaginator
    {
        $query = Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
            ->published();

        $this->applyFilters($query, $data);
        $this->applySort($query, $data->sort);

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Published experiences outside the given city, for the "Go beyond"
     * section — top-rated first, so the related picks feel curated.
     *
     * @return Collection<int, Experience>
     */
    public function beyondCity(string $city, int $limit = 6): Collection
    {
        return Experience::query()
            ->with(ExperienceCardPresenter::eagerLoads())
            ->published()
            ->whereRaw('LOWER(location_city) != ?', [mb_strtolower($city)])
            ->orderByDesc('is_top_rated')
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get();
    }

    private function applyFilters(Builder $query, ExperienceSearchData $data): void
    {
        if ($data->search) {
            $search = mb_strtolower($data->search);
            $like = '%'.$search.'%';
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale', 'en');

            $query->where(function (Builder $query) use ($like, $locale, $fallbackLocale, $data): void {
                $query
                    ->whereRaw('LOWER(location_city) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(category) LIKE ?', [$like])
                    ->orWhere("title->{$locale}", 'like', '%'.$data->search.'%')
                    ->orWhere("description->{$locale}", 'like', '%'.$data->search.'%');

                if ($fallbackLocale !== $locale) {
                    $query
                        ->orWhere("title->{$fallbackLocale}", 'like', '%'.$data->search.'%')
                        ->orWhere("description->{$fallbackLocale}", 'like', '%'.$data->search.'%');
                }
            });
        }

        if ($data->city) {
            $query->whereRaw('LOWER(location_city) = ?', [mb_strtolower($data->city)]);
        }

        if ($data->categories !== []) {
            $query->whereIn('category', $data->categories);
        }

        if ($data->date || $data->dateFrom || $data->dateTo) {
            $dateFrom = $data->dateFrom ?: $data->date;
            $dateTo = $data->dateTo ?: $dateFrom;

            $query->whereHas('availabilities', function (Builder $query) use ($data, $dateFrom, $dateTo): void {
                $query
                    ->where('is_active', true)
                    ->whereColumn('booked_seats', '<', 'max_seats')
                    ->when($dateFrom && $dateTo, fn (Builder $query): Builder => $query->whereDate('date', '>=', $dateFrom)->whereDate('date', '<=', $dateTo))
                    ->when($dateFrom && ! $dateTo, fn (Builder $query): Builder => $query->whereDate('date', '>=', $dateFrom))
                    ->when(! $dateFrom && $dateTo, fn (Builder $query): Builder => $query->whereDate('date', '<=', $dateTo))
                    ->when($data->participants !== null, function (Builder $query) use ($data): void {
                        $query->whereRaw('(max_seats - booked_seats - held_seats) >= ?', [$data->participants]);
                    });
            });
        }

        if ($data->participants !== null) {
            $query->where('max_group_size', '>=', $data->participants);
        }

        if ($data->minPrice !== null) {
            $query->where('price_per_person', '>=', $data->minPrice);
        }

        if ($data->maxPrice !== null) {
            $query->where('price_per_person', '<=', $data->maxPrice);
        }

        if ($data->privateAvailable) {
            $query->whereNotNull('private_price');
        }

        if ($data->pickupAvailable) {
            $query->where('pickup_enabled', true);
        }

        match ($data->duration) {
            'half' => $query->where('duration_hours', '<', 4),
            'full' => $query->whereBetween('duration_hours', [4, 8]),
            'multi' => $query->where('duration_hours', '>', 8),
            default => null,
        };
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_low' => $query->orderBy('price_per_person')->orderByDesc('rating_avg'),
            'price_high' => $query->orderByDesc('price_per_person')->orderByDesc('rating_avg'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count'),
            'newest' => $query->latest(),
            default => $query
                ->orderByDesc('is_top_rated')
                ->orderByDesc('rating_avg')
                ->orderByDesc('reviews_count')
                ->latest(),
        };
    }
}
