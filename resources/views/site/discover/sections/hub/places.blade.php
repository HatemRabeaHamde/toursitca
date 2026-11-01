{{-- Chapter one · Places — interactive map + destination list (Alpine discoverMap). --}}
<x-site.organisms.section id="destinations" bg="sand" :divider="true" labelledby="destinations-title" class="lv-zellige-end">
    <x-site.molecules.section-header id="destinations-title"
        :eyebrow="__('discover.hub.places.eyebrow')"
        :before="__('discover.hub.places.title')"
        :accent="__('discover.hub.places.accent')"
        :lede="__('discover.hub.places.lede')"
        :link-href="$page->urls->experiences()"
        :link-label="__('discover.hub.places.link')" />

    <div class="mt-10 grid items-start gap-6 lg:grid-cols-[minmax(0,660fr)_minmax(0,508fr)] lg:gap-8"
         x-data="discoverMap({ initial: @js($page->initialCity()) })">
        <figure class="lv-map hidden md:block" data-reveal="image">
            <svg class="lv-map__terrain" viewBox="0 0 660 600" preserveAspectRatio="none" aria-hidden="true">
                <ellipse cx="590" cy="70" rx="230" ry="190" />
                <ellipse cx="40" cy="600" rx="250" ry="200" />
            </svg>
            <svg class="lv-map__routes" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                @foreach($page->mapLines() as $line)
                    <line x1="{{ $line['x1'] }}" y1="{{ $line['y1'] }}" x2="{{ $line['x2'] }}" y2="{{ $line['y2'] }}" />
                @endforeach
            </svg>

            <ul aria-label="{{ __('discover.hub.places.map_label') }}">
                @foreach($page->mapPins() as $city)
                    <li class="lv-map__place" style="--x: {{ $city['x'] }}%; --y: {{ $city['y'] }}%">
                        <a href="{{ $city['href'] }}" class="lv-map__pin" :class="{ 'is-active': isActive(@js($city['slug'])) }"
                           @mouseenter="select(@js($city['slug']))" @focus="select(@js($city['slug']))" tabindex="-1">
                            <span class="lv-map__dot" aria-hidden="true"></span>
                            <span class="lv-map__label lv-t-label-xs">{{ $city['name'] }}</span>
                        </a>

                        <a href="{{ $city['href'] }}" x-cloak x-show="isActive(@js($city['slug']))" x-transition.opacity.duration.300ms
                           @class(['lv-map__preview group', 'is-above' => $city['opens_above'], 'is-start' => $city['opens_start']])>
                            <img src="{{ $city['image'] }}" alt="" width="72" height="72" loading="lazy">
                            <span class="flex flex-col gap-1">
                                <span class="lv-t-h6 lv-tone-ink">{{ $city['name'] }}</span>
                                @if($city['known_for'])<span class="lv-t-caption lv-tone-muted">{{ $city['known_for'] }}</span>@endif
                                <span class="lv-arrow-link lv-t-label-s lv-tone-terracotta">
                                    <span>{{ $city['cta'] ?? __('discover.actions.open_guide') }}</span>
                                    <x-site.atoms.icon name="arrow-right-ink" :size="14" />
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <figcaption class="lv-map__note lv-t-caption lv-tone-muted">{{ __('discover.hub.places.note') }}</figcaption>
        </figure>

        <ul class="flex flex-col gap-3" data-reveal="stagger">
            @foreach($page->cities as $city)
                <li><x-site.molecules.city-option :city="$city" /></li>
            @endforeach
        </ul>
    </div>
</x-site.organisms.section>
