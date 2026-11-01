<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\Models\GuideBlock;
use App\Domain\Discover\Models\Interest;
use App\Domain\Discover\ViewModels\InterestPageViewModel;
use App\Domain\Landing\Models\Destination;

/** Assembles an interest guide page (Figma 77:1564 — Cuisine). */
final class InterestGuideService
{
    public function __construct(
        private readonly DiscoverUrls $urls,
        private readonly GuideBlocks $blocks,
        private readonly GuideExperienceCards $experiences,
        private readonly StoryCards $stories,
        private readonly HostCards $hosts,
    ) {}

    public function build(Interest $interest): InterestPageViewModel
    {
        $locale = app()->getLocale();
        $name = $interest->getTranslation('name', $locale, true);
        $categories = (array) ($interest->category_slugs ?? []);
        $blocks = $this->blocks->for($interest);

        $regions = $interest->destinations()->where('is_active', true)->get()
            ->map(fn (Destination $city): array => [
                'href' => $city->guide ? $this->urls->destination($city->slug) : $this->urls->experiences(['city' => $city->city]),
                'title' => $city->getTranslation('name', $locale, true),
                'body' => $city->pivot->getTranslation('note', $locale, true),
                'image' => (string) $city->image_url,
                'cta' => $city->guide ? null : __('discover.actions.see_experiences'),
            ])
            ->all();

        return new InterestPageViewModel(
            name: $name,
            heroImage: (string) ($interest->hero_image_url ?: $interest->image_url),
            guide: (array) $interest->getTranslation('guide', $locale, true),
            breadcrumb: $this->urls->breadcrumb([
                ['label' => __('discover.breadcrumb.discover'), 'href' => $this->urls->hub()],
                ['label' => __('discover.breadcrumb.interests'), 'href' => $this->urls->hub('interests')],
                ['label' => $name, 'href' => null],
            ]),
            origins: $blocks[GuideBlock::ORIGIN] ?? [],
            dishes: $blocks[GuideBlock::DISH] ?? [],
            also: ($blocks[GuideBlock::ALSO] ?? [])[0] ?? null,
            notes: $blocks[GuideBlock::TIP] ?? [],
            regions: $regions,
            experiences: $this->experiences->forCategories($categories, 3),
            stories: $this->stories->forInterest($interest->id, 1),
            hosts: $this->hosts->forCategories($categories, 2),
            experiencesUrl: $this->urls->experiences(['category' => $categories]),
            destinationsUrl: $this->urls->hub('destinations'),
            storiesUrl: $this->urls->landing('stories'),
        );
    }
}
