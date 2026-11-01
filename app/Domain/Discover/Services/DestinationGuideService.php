<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\Models\GuideBlock;
use App\Domain\Discover\ViewModels\DestinationPageViewModel;
use App\Domain\Landing\Models\Destination;

/** Assembles a destination guide page (Figma 76:1269 — Marrakech). */
final class DestinationGuideService
{
    public function __construct(
        private readonly DiscoverUrls $urls,
        private readonly GuideBlocks $blocks,
        private readonly GuideExperienceCards $experiences,
        private readonly StoryCards $stories,
        private readonly HostCards $hosts,
    ) {}

    public function build(Destination $destination): DestinationPageViewModel
    {
        $locale = app()->getLocale();
        $name = $destination->getTranslation('name', $locale, true);
        $blocks = $this->blocks->for($destination);

        return new DestinationPageViewModel(
            name: $name,
            region: (string) $destination->region,
            heroImage: (string) ($destination->hero_image_url ?: $destination->image_url),
            guide: (array) $destination->getTranslation('guide', $locale, true),
            breadcrumb: $this->urls->breadcrumb([
                ['label' => __('discover.breadcrumb.discover'), 'href' => $this->urls->hub()],
                ['label' => __('discover.breadcrumb.destinations'), 'href' => $this->urls->hub('destinations')],
                ['label' => $name, 'href' => null],
            ]),
            landmarks: $blocks[GuideBlock::LANDMARK] ?? [],
            crafts: $blocks[GuideBlock::CRAFT] ?? [],
            dishes: $blocks[GuideBlock::DISH] ?? [],
            tips: $blocks[GuideBlock::TIP] ?? [],
            experiences: $this->experiences->forCity((string) $destination->city, 3),
            stories: $this->stories->forDestination($destination->id, 2),
            hosts: $this->hosts->inCity((string) $destination->city, 2),
            experiencesUrl: $this->urls->experiences(['city' => $destination->city]),
            guideUrl: $this->urls->landing('know'),
        );
    }
}
