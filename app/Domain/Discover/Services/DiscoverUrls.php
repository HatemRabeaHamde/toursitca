<?php

namespace App\Domain\Discover\Services;

/** Single place that knows the URL shape of the discovery pages. */
final class DiscoverUrls
{
    public function hub(string $anchor = ''): string
    {
        return route('site.discover.index', ['locale' => app()->getLocale()]).($anchor !== '' ? '#'.$anchor : '');
    }

    public function destination(string $slug): string
    {
        return route('site.discover.destinations.show', ['locale' => app()->getLocale(), 'destination' => $slug]);
    }

    public function interest(string $slug): string
    {
        return route('site.discover.interests.show', ['locale' => app()->getLocale(), 'interest' => $slug]);
    }

    public function hosts(): string
    {
        return route('site.hosts.index', ['locale' => app()->getLocale()]);
    }

    public function host(string $slug): string
    {
        return route('site.hosts.show', ['locale' => app()->getLocale(), 'host' => $slug]);
    }

    public function experiences(array $query = []): string
    {
        return route('site.experiences.index', ['locale' => app()->getLocale(), ...$query]);
    }

    public function reviews(array $query = []): string
    {
        return route('site.reviews.index', ['locale' => app()->getLocale(), ...$query]);
    }

    public function landing(string $anchor = ''): string
    {
        return route('site.home', ['locale' => app()->getLocale()]).($anchor !== '' ? '#'.$anchor : '');
    }

    /** @return list<array{label: string, href: ?string}> */
    public function breadcrumb(array $trail): array
    {
        return [
            ['label' => __('discover.breadcrumb.home'), 'href' => $this->landing()],
            ...$trail,
        ];
    }
}
