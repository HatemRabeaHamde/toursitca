<?php

namespace App\Domain\Discover\Services;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceCardPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use NumberFormatter;

/**
 * Experience cards for the discovery pages (Figma "Card / Experience").
 * Reuses ExperienceCardPresenter for title/duration/price/rating and adds
 * what the editorial card shows on top: summary, named host and licence.
 *
 * @phpstan-type Card array{href: string, title: string, image: string, location: string, summary: ?string,
 *     host: ?array{name: string, role: ?string, avatar: ?string}, rating: ?string, reviews: ?int, licence: ?string,
 *     duration: string, group: ?string, price: string, featured: bool, wishlistUrl: string}
 */
final class GuideExperienceCards
{
    public function __construct(private readonly ExperienceCardPresenter $presenter) {}

    /** @return list<Card> */
    public function featured(int $limit = 3): array
    {
        return $this->cards(Experience::query(), $limit);
    }

    /** @return list<Card> */
    public function forCity(string $city, int $limit = 3): array
    {
        return $this->cards(
            Experience::query()->whereRaw('LOWER(location_city) = ?', [mb_strtolower($city)]),
            $limit,
        );
    }

    /** @param list<string> $categories @return list<Card> */
    public function forCategories(array $categories, int $limit = 3): array
    {
        if ($categories === []) {
            return [];
        }

        return $this->cards(Experience::query()->whereIn('category', $categories), $limit);
    }

    /** @return list<Card> */
    public function forHost(int $hostId, int $limit = 6): array
    {
        return $this->cards(Experience::query()->where('host_id', $hostId), $limit);
    }

    /** @return list<Card> */
    private function cards(Builder $query, int $limit): array
    {
        return $query
            ->published()
            ->with([...ExperienceCardPresenter::eagerLoads(), 'host'])
            ->orderByDesc('is_top_rated')
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get()
            ->map(fn (Experience $experience): array => $this->present($experience))
            ->all();
    }

    /** @return Card */
    private function present(Experience $experience): array
    {
        $card = $this->presenter->present($experience);
        $host = $experience->publicHost();
        $locale = app()->getLocale();

        return [
            'href' => $card->showUrl,
            'title' => $card->title,
            'image' => $card->thumbnailUrl ?: (string) config('landing.images.card_fallback'),
            'location' => $card->city,
            'summary' => Str::limit(trim(strip_tags((string) $experience->getTranslation('description', $locale, true))), 110),
            'host' => $host
                ? ['name' => $host->name, 'role' => $host->getTranslation('role', $locale, true), 'avatar' => $host->avatar_url ?: $host->photo_url]
                : ($card->agencyName ? ['name' => $card->agencyName, 'role' => null, 'avatar' => null] : null),
            'rating' => $card->reviewsCount > 0 ? $card->ratingAvg : null,
            'reviews' => $card->reviewsCount > 0 ? $card->reviewsCount : null,
            'licence' => $host?->verifiedLicence(),
            'duration' => $card->durationLabel,
            'group' => in_array(__('ui.tags.small_group'), $card->tags, true) ? __('discover.experience.small_group') : null,
            'price' => $this->money((float) $experience->price_per_person, $card->currency),
            'featured' => (bool) $experience->is_top_rated,
            'wishlistUrl' => route('site.wishlist.toggle', ['locale' => $locale, 'experience' => $experience->id]),
        ];
    }

    private function money(float $amount, string $currency): string
    {
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, fmod($amount, 1.0) === 0.0 ? 0 : 2);

        return (string) $formatter->formatCurrency($amount, $currency);
    }
}
