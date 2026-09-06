<?php

namespace App\Domain\Experience\Services;

use App\Domain\Experience\DTOs\ExperienceCardData;
use Illuminate\Support\Collection;

final class LandingDemoContentFactory
{
    public function stats(): array
    {
        return [
            'experiences_count' => 24,
            'cities_count' => 8,
            'avg_rating' => '4.8',
            'happy_travelers' => 640,
        ];
    }

    public function topAgencies(): Collection
    {
        return collect([
            [
                'name' => __('ui.landing.demo.agencies.medina.name'),
                'city' => __('ui.landing.demo.cities.marrakesh'),
                'experiences_count' => 8,
                'rating_avg' => '4.9',
                'reviews_count' => 214,
                'is_verified' => true,
                'image_url' => $this->destinationImage('Marrakesh'),
            ],
            [
                'name' => __('ui.landing.demo.agencies.desert.name'),
                'city' => __('ui.landing.demo.cities.sahara'),
                'experiences_count' => 6,
                'rating_avg' => '4.8',
                'reviews_count' => 176,
                'is_verified' => true,
                'image_url' => $this->destinationImage('Sahara'),
            ],
            [
                'name' => __('ui.landing.demo.agencies.atlas.name'),
                'city' => __('ui.landing.demo.cities.atlas'),
                'experiences_count' => 5,
                'rating_avg' => '4.7',
                'reviews_count' => 132,
                'is_verified' => true,
                'image_url' => $this->destinationImage('Atlas'),
            ],
            [
                'name' => __('ui.landing.demo.agencies.coast.name'),
                'city' => __('ui.landing.demo.cities.essaouira'),
                'experiences_count' => 4,
                'rating_avg' => '4.8',
                'reviews_count' => 98,
                'is_verified' => true,
                'image_url' => $this->destinationImage('Essaouira'),
            ],
        ]);
    }

    public function travelReels(): Collection
    {
        return collect([
            [
                'title' => __('ui.landing.demo.reels.medina'),
                'city' => __('ui.landing.demo.cities.marrakesh'),
                'thumbnail_url' => config('landing.images.demo_reels.medina'),
                'video_url' => null,
            ],
            [
                'title' => __('ui.landing.demo.reels.desert'),
                'city' => __('ui.landing.demo.cities.sahara'),
                'thumbnail_url' => config('landing.images.demo_reels.desert'),
                'video_url' => null,
            ],
            [
                'title' => __('ui.landing.demo.reels.blue_city'),
                'city' => __('ui.landing.demo.cities.chefchaouen'),
                'thumbnail_url' => config('landing.images.demo_reels.blue_city'),
                'video_url' => null,
            ],
            [
                'title' => __('ui.landing.demo.reels.coast'),
                'city' => __('ui.landing.demo.cities.essaouira'),
                'thumbnail_url' => config('landing.images.demo_reels.coast'),
                'video_url' => null,
            ],
        ]);
    }

    public function testimonials(): Collection
    {
        return collect([
            [
                'author_name' => __('ui.landing.demo.testimonials.one.author'),
                'author_country' => __('ui.landing.demo.testimonials.one.country'),
                'body' => __('ui.landing.demo.testimonials.one.body'),
                'experience_title' => __('ui.landing.demo.cards.marrakech_food.title'),
                'rating' => '5.0',
                'avatar_url' => null,
            ],
            [
                'author_name' => __('ui.landing.demo.testimonials.two.author'),
                'author_country' => __('ui.landing.demo.testimonials.two.country'),
                'body' => __('ui.landing.demo.testimonials.two.body'),
                'experience_title' => __('ui.landing.demo.cards.sahara_camp.title'),
                'rating' => '5.0',
                'avatar_url' => null,
            ],
            [
                'author_name' => __('ui.landing.demo.testimonials.three.author'),
                'author_country' => __('ui.landing.demo.testimonials.three.country'),
                'body' => __('ui.landing.demo.testimonials.three.body'),
                'experience_title' => __('ui.landing.demo.cards.chefchaouen_photo.title'),
                'rating' => '4.0',
                'avatar_url' => null,
            ],
        ]);
    }

    public function cards(): Collection
    {
        return collect([
            [
                'id' => -1,
                'title' => __('ui.landing.demo.cards.marrakech_food.title'),
                'city' => __('ui.landing.demo.cities.marrakesh'),
                'category' => __('ui.categories.food'),
                'image' => config('landing.images.demo_cards.marrakech_food'),
                'duration' => 3.5,
                'rating' => '4.9',
                'reviews' => 128,
                'price' => '52.00',
                'original' => '65.00',
                'category_filter' => 'food',
            ],
            [
                'id' => -2,
                'title' => __('ui.landing.demo.cards.sahara_camp.title'),
                'city' => __('ui.landing.demo.cities.sahara'),
                'category' => __('ui.categories.desert'),
                'image' => config('landing.images.demo_cards.sahara_camp'),
                'duration' => 10,
                'rating' => '4.8',
                'reviews' => 94,
                'price' => '120.00',
                'original' => '145.00',
                'category_filter' => 'desert',
            ],
            [
                'id' => -3,
                'title' => __('ui.landing.demo.cards.fes_workshop.title'),
                'city' => __('ui.landing.demo.cities.fes'),
                'category' => __('ui.categories.workshop'),
                'image' => config('landing.images.demo_cards.fes_workshop'),
                'duration' => 2.5,
                'rating' => '4.7',
                'reviews' => 61,
                'price' => '38.00',
                'original' => null,
                'category_filter' => 'workshop',
            ],
            [
                'id' => -4,
                'title' => __('ui.landing.demo.cards.chefchaouen_photo.title'),
                'city' => __('ui.landing.demo.cities.chefchaouen'),
                'category' => __('ui.categories.photography'),
                'image' => config('landing.images.demo_cards.chefchaouen_photo'),
                'duration' => 4,
                'rating' => '4.9',
                'reviews' => 73,
                'price' => '44.00',
                'original' => null,
                'category_filter' => 'photography',
            ],
            [
                'id' => -5,
                'title' => __('ui.landing.demo.cards.essaouira_coast.title'),
                'city' => __('ui.landing.demo.cities.essaouira'),
                'category' => __('ui.categories.water'),
                'image' => config('landing.images.demo_cards.essaouira_coast'),
                'duration' => 6,
                'rating' => '4.6',
                'reviews' => 48,
                'price' => '58.00',
                'original' => '70.00',
                'category_filter' => 'water',
            ],
            [
                'id' => -6,
                'title' => __('ui.landing.demo.cards.atlas_hike.title'),
                'city' => __('ui.landing.demo.cities.atlas'),
                'category' => __('ui.categories.mountain'),
                'image' => config('landing.images.demo_cards.atlas_hike'),
                'duration' => 7,
                'rating' => '4.8',
                'reviews' => 86,
                'price' => '75.00',
                'original' => null,
                'category_filter' => 'mountain',
            ],
        ])->map(fn (array $card): ExperienceCardData => $this->card($card));
    }

    private function card(array $card): ExperienceCardData
    {
        return new ExperienceCardData(
            id: $card['id'],
            slug: null,
            title: $card['title'],
            city: $card['city'],
            category: $card['category'],
            agencyName: null,
            thumbnailUrl: $card['image'],
            durationLabel: $this->durationLabel((float) $card['duration']),
            ratingAvg: $card['rating'],
            reviewsCount: $card['reviews'],
            badges: $card['original'] !== null ? [__('ui.badges.deal')] : [__('ui.badges.top_rated')],
            features: [__('ui.features.private_option_available')],
            tags: [__('ui.tags.small_group')],
            priceFrom: $card['price'],
            originalPrice: $card['original'],
            currency: config('payment.currency', 'MAD'),
            isPrivateAvailable: true,
            isPickupAvailable: false,
            isDeal: $card['original'] !== null,
            dealEndsAt: $card['original'] !== null ? now()->addDays(6) : null,
            availabilityStatus: 'available',
            spotsLeft: null,
            showUrl: route('site.experiences.index', [
                'locale' => app()->getLocale(),
                'city' => $card['city'],
                'category' => $card['category_filter'],
            ]),
        );
    }

    private function durationLabel(float $hours): string
    {
        $normalized = rtrim(rtrim(number_format($hours, 1), '0'), '.');

        return trans_choice('ui.units.hours', (int) ceil($hours), ['count' => $normalized]);
    }

    private function destinationImage(?string $city): string
    {
        $normalized = mb_strtolower((string) $city);

        $images = config('landing.images.destinations', []);

        return match (true) {
            str_contains($normalized, 'fes') || str_contains($normalized, 'fez') => $images['fes'],
            str_contains($normalized, 'sahara') => $images['sahara'],
            str_contains($normalized, 'zagora') => $images['zagora'],
            str_contains($normalized, 'chefchaouen') => $images['chefchaouen'],
            str_contains($normalized, 'essaouira') => $images['essaouira'],
            default => $images['default'],
        };
    }
}
