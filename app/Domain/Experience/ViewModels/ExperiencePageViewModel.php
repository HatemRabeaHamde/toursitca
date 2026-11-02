<?php

namespace App\Domain\Experience\ViewModels;

/**
 * Experience detail (Figma 09 · Erg Chigaga) ready for Blade.
 * Built by ExperiencePageService — no queries happen here.
 */
final readonly class ExperiencePageViewModel
{
    /**
     * @param  list<array{label: string, href: ?string}>  $breadcrumb
     * @param  array{image: string, location: string, meta: list<array{icon: string, label: string}>, rating: ?string, reviews: int, verified: bool}  $hero
     * @param  list<string>  $gallery
     * @param  list<string>  $highlights
     * @param  list<array{label: string, title: string, description: ?string, image: ?string}>  $itinerary
     * @param  array{items: list<string>, excluded: ?string}  $included
     * @param  array{name: string, firstName: string, avatar: ?string, line: string, verified: bool, licence: ?string, href: string}|null  $host
     * @param  array{average: string, count: int, featured: ?array<string, mixed>, href: string}|null  $reviews
     * @param  array<string, mixed>  $booking
     * @param  list<array<string, mixed>>  $related
     * @param  array{place: string, note: ?string, mapUrl: ?string, stops: list<array{name: string, x: float, y: float}>}|null  $location
     */
    public function __construct(
        public string $title,
        public string $description,
        public array $breadcrumb,
        public array $hero,
        public array $gallery,
        public array $highlights,
        public array $itinerary,
        public array $included,
        public ?array $host,
        public ?array $reviews,
        public array $booking,
        public array $related = [],
        public ?array $location = null,
    ) {}

    /** Hero title, its second half set in gold italic: [lead, accent]. */
    public function titleParts(): array
    {
        $words = preg_split('/\s+/u', trim($this->title));

        if (count($words) < 2) {
            return [$this->title, null];
        }

        $lead = (int) ceil(count($words) / 2);

        return [implode(' ', array_slice($words, 0, $lead)), implode(' ', array_slice($words, $lead))];
    }

    /** Section tabs of the v1 layout — only for sections that have content. */
    public function tabsV1(): array
    {
        return array_values(array_filter([
            $this->highlights ? ['id' => 'overview', 'label' => __('experiences.detail.tabs.overview')] : null,
            $this->booking['options'] !== [] ? ['id' => 'options', 'label' => __('booking_flow.options.tab')] : null,
            $this->itinerary ? ['id' => 'itinerary', 'label' => __('experiences.detail.tabs.itinerary')] : null,
            $this->included['items'] ? ['id' => 'included', 'label' => __('experiences.detail.included.title')] : null,
            $this->host ? ['id' => 'host', 'label' => __('experiences.detail.v1.tabs.host')] : null,
            $this->location ? ['id' => 'location', 'label' => __('experiences.detail.v1.tabs.location')] : null,
            $this->reviews ? ['id' => 'reviews', 'label' => __('experiences.detail.tabs.reviews')] : null,
        ]));
    }

    /** Sticky section tabs — only for sections that have content. */
    public function tabs(): array
    {
        return array_values(array_filter([
            ['id' => 'overview', 'label' => __('experiences.detail.tabs.overview')],
            $this->itinerary ? ['id' => 'itinerary', 'label' => __('experiences.detail.tabs.itinerary')] : null,
            $this->host ? ['id' => 'host', 'label' => __('experiences.detail.tabs.host')] : null,
            $this->reviews ? ['id' => 'reviews', 'label' => __('experiences.detail.tabs.reviews')] : null,
        ]));
    }
}
