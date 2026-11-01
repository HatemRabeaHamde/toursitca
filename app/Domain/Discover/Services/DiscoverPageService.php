<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\Models\Interest;
use App\Domain\Discover\ViewModels\DiscoverPageViewModel;
use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Services\LandingContentService;

/** Assembles the Discover Morocco hub (Figma 75:952). */
final class DiscoverPageService
{
    public function __construct(
        private readonly DiscoverUrls $urls,
        private readonly GuideExperienceCards $experiences,
        private readonly StoryCards $stories,
        private readonly HostCards $hosts,
        private readonly LandingContentService $landingContent,
    ) {}

    private function fallbackImage(string $slug): string
    {
        $images = (array) config('landing.images.destinations', []);
        $key = collect(array_keys($images))->first(fn (string $key): bool => str_contains($slug, $key)) ?? 'default';

        return (string) ($images[$key] ?? config('landing.images.card_fallback'));
    }

    public function build(): DiscoverPageViewModel
    {
        $locale = app()->getLocale();

        $cities = Destination::query()->active()->whereNotNull('map_x')->orderBy('sort_order')->get()
            ->map(fn (Destination $city): array => [
                'slug' => $city->slug,
                'name' => $city->getTranslation('name', $locale, true),
                'summary' => $city->getTranslation('summary', $locale, true),
                'known_for' => data_get($city->getTranslation('guide', $locale, true), 'facts.known_for'),
                'image' => (string) ($city->image_url ?: $this->fallbackImage($city->slug)),
                'tags' => (array) ($city->getTranslation('tags', $locale, true) ?: []),
                'href' => $city->guide ? $this->urls->destination($city->slug) : $this->urls->experiences(['city' => $city->city]),
                'cta' => $city->guide ? null : __('discover.actions.see_experiences'),
                'x' => $city->map_x,
                'y' => $city->map_y,
            ])
            ->values()
            ->all();

        $interests = Interest::query()->active()->sorted()->limit(6)->get()
            ->map(fn (Interest $interest): array => [
                'href' => $interest->guide ? $this->urls->interest($interest->slug) : $this->urls->experiences(['category' => (array) $interest->category_slugs]),
                'title' => $interest->getTranslation('name', $locale, true),
                'body' => $interest->getTranslation('summary', $locale, true),
                'image' => (string) $interest->image_url,
                'cta' => $interest->guide ? null : __('discover.actions.see_experiences'),
            ])
            ->all();

        $faqs = $this->landingContent->faqs(4);

        return new DiscoverPageViewModel(
            urls: $this->urls,
            cities: $cities,
            mapRoutes: (array) config('discover.map.routes', []),
            interests: $interests,
            stories: $this->stories->featured(3),
            experiences: $this->experiences->featured(3),
            hosts: $this->hosts->featured(4),
            faqs: ($faqs->isNotEmpty() ? $faqs : collect(__('ui.landing.faq')))->take(4)->values()->all(),
        );
    }
}
