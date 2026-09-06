<?php

namespace App\Domain\Landing\Services;

use App\Domain\Experience\Models\Experience;
use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Models\ExperienceCategory;
use App\Domain\Landing\Models\LandingFaq;
use App\Domain\Landing\Models\Landmark;
use App\Domain\Landing\Models\Testimonial;
use App\Domain\Landing\Models\TravelReel;
use Illuminate\Support\Collection;

final class LandingContentService
{
    public function categories(int $limit = 8): Collection
    {
        return ExperienceCategory::query()
            ->active()
            ->sorted()
            ->limit($limit)
            ->get()
            ->map(fn (ExperienceCategory $category): array => [
                'slug' => $category->slug,
                'name' => $category->getTranslation('name', app()->getLocale(), false),
                'image_url' => $category->image_url,
            ]);
    }

    public function destinations(int $limit = 5): Collection
    {
        return Destination::query()
            ->active()
            ->sorted()
            ->limit($limit)
            ->get()
            ->map(fn (Destination $destination): array => [
                'slug' => $destination->slug,
                'name' => $destination->getTranslation('name', app()->getLocale(), false),
                'city' => $destination->city,
                'image_url' => $destination->image_url,
                'is_featured' => (bool) $destination->is_featured,
            ]);
    }

    public function landmarksForCity(string $city, int $limit = 8): Collection
    {
        $landmarks = Landmark::query()
            ->active()
            ->forCity($city)
            ->sorted()
            ->limit($limit)
            ->get();

        if ($landmarks->isEmpty()) {
            return $landmarks;
        }

        $activitiesCount = Experience::query()
            ->published()
            ->whereRaw('LOWER(location_city) = ?', [mb_strtolower($city)])
            ->count();

        return $landmarks->map(fn (Landmark $landmark): array => [
            'name' => $landmark->getTranslation('name', app()->getLocale(), false),
            'image_url' => $landmark->image_url ?: $this->landmarkImage($landmark->category),
            'activities_count' => $activitiesCount,
        ]);
    }

    private function landmarkImage(?string $category): string
    {
        return match ($category) {
            'garden' => 'https://images.unsplash.com/photo-1597212618440-806262de4f6b?w=600&q=80',
            'palace' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=600&q=80',
            'museum' => 'https://images.unsplash.com/photo-1554907984-15263bfd63bd?w=600&q=80',
            'historic' => 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?w=600&q=80',
            default => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80',
        };
    }

    public function faqs(int $limit = 8): Collection
    {
        return LandingFaq::query()
            ->active()
            ->sorted()
            ->limit($limit)
            ->get()
            ->map(fn (LandingFaq $faq): array => [
                'question' => $faq->getTranslation('question', app()->getLocale(), false),
                'answer' => $faq->getTranslation('answer', app()->getLocale(), false),
            ]);
    }

    public function travelReels(int $limit = 8): Collection
    {
        return TravelReel::query()
            ->active()
            ->sorted()
            ->limit($limit)
            ->get()
            ->map(fn (TravelReel $reel): array => [
                'title' => $reel->getTranslation('title', app()->getLocale(), false),
                'city' => $reel->city,
                'thumbnail_url' => $reel->thumbnail_url,
                'video_url' => $reel->video_url,
            ]);
    }

    public function testimonials(int $limit = 3): Collection
    {
        return Testimonial::query()
            ->active()
            ->sorted()
            ->limit($limit)
            ->get()
            ->map(fn (Testimonial $testimonial): array => [
                'author_name' => $testimonial->getTranslation('author_name', app()->getLocale(), false),
                'author_country' => $testimonial->author_country,
                'body' => $testimonial->getTranslation('body', app()->getLocale(), false),
                'experience_title' => $testimonial->experience_title,
                'rating' => number_format((float) $testimonial->rating, 1),
                'avatar_url' => $testimonial->avatar_url,
            ]);
    }
}
