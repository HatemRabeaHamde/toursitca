<?php

namespace App\Domain\Discover\Services;

use App\Domain\Discover\Models\Host;
use App\Domain\Discover\ViewModels\HostProfileViewModel;
use App\Domain\Review\Models\Review;
use App\Domain\Review\Services\ReviewSummary;
use Illuminate\Support\Str;
use Locale;

/** Host profile: who they are, what they lead, what travellers said. */
final class HostProfileService
{
    public function __construct(
        private readonly GuideExperienceCards $experiences,
        private readonly HostCards $hosts,
        private readonly ReviewSummary $reviewSummary,
        private readonly DiscoverUrls $urls,
    ) {}

    public function build(Host $host): HostProfileViewModel
    {
        $locale = app()->getLocale();
        $host->loadMissing('destination');
        $region = $host->destination?->getTranslation('name', $locale, true);

        return new HostProfileViewModel(
            name: $host->name,
            role: (string) $host->getTranslation('role', $locale, true),
            quote: $host->getTranslation('quote', $locale, true) ?: null,
            photo: $this->url($host->photo_url ?: $host->avatar_url),
            cover: $this->url($host->cover_url) ?? asset(config('discover.images.hosts_hero')),
            place: collect([$host->city, $region])->filter()->unique()->implode(', ') ?: null,
            languages: $this->languages($host),
            breadcrumb: $this->urls->breadcrumb([
                ['label' => __('hosts.breadcrumb'), 'href' => $this->urls->hosts()],
                ['label' => $host->name, 'href' => null],
            ]),
            story: $this->story($host, $locale),
            skills: array_values(array_filter((array) $host->getTranslation('skills', $locale, true))),
            experiences: $this->experiences->forHost($host->id),
            reviews: $this->reviews($host, $locale),
            trust: $this->trust($host),
            moreHosts: $this->hosts->others($host),
            hostsUrl: $this->urls->hosts(),
            vettingUrl: $this->urls->hosts().'#vetting',
        );
    }

    /** @return list<string> */
    private function story(Host $host, string $locale): array
    {
        $story = array_values(array_filter((array) $host->getTranslation('story', $locale, true)));

        if ($story !== []) {
            return $story;
        }

        return array_values(array_filter([$host->getTranslation('bio', $locale, true)]));
    }

    private function reviews(Host $host, string $locale): ?array
    {
        $reviews = Review::query()
            ->where('is_visible', true)
            ->whereHas('experience', fn ($query) => $query->where('host_id', $host->id))
            ->with(['booking.availability', 'user'])
            ->get();

        if ($reviews->isEmpty()) {
            return null;
        }

        return [
            'average' => number_format((float) $reviews->avg('rating'), 1),
            'count' => $reviews->count(),
            'breakdown' => $this->reviewSummary->breakdown($reviews),
            'cards' => $this->reviewSummary->cards($reviews, $locale),
            'href' => $this->urls->reviews(['host' => $host->slug]).'#results',
        ];
    }

    /** Only what is true of this host; the wording matches the vetting steps on the hosts page. */
    private function trust(Host $host): array
    {
        return array_values(array_filter([
            $host->is_verified ? ['icon' => 'shield-success', 'title' => __('hosts.profile.verified'), 'body' => __('hosts.vetting.steps.1.body')] : null,
            $host->verifiedLicence() ? ['icon' => 'card-ink', 'title' => __('experiences.detail.host.licence', ['number' => $host->verifiedLicence()]), 'body' => __('hosts.vetting.steps.2.body')] : null,
            $host->partner_since ? ['icon' => 'pin-terracotta-18', 'title' => __('hosts.profile.partner_since', ['year' => $host->partner_since]), 'body' => $host->city ? __('hosts.profile.lives_in', ['city' => $host->city]) : null] : null,
            $host->is_verified ? ['icon' => 'star', 'title' => __('hosts.profile.reviewed'), 'body' => __('hosts.vetting.steps.3.body')] : null,
        ]));
    }

    private function languages(Host $host): ?string
    {
        return collect($host->languages)
            ->map(fn (string $code): string => Str::ucfirst(Locale::getDisplayLanguage($code, app()->getLocale())))
            ->implode(', ') ?: null;
    }

    private function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://', '/']) ? $path : asset($path);
    }
}
