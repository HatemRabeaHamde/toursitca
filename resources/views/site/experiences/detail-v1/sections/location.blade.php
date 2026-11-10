{{-- Real route map (meeting point + stops with real coordinates) + where the trip starts. Alpine experienceRouteMap. --}}
@php
    $location = $page->location;
    $hasMap = $location['meet'] || $location['stops'];
    $stopNames = collect($location['stops'])->pluck('name');
    $mapConfig = [
        'meet' => $location['meet'],
        'meetName' => $location['note'] ? null : $location['place'],
        'stops' => $location['stops'],
        'labels' => [
            'meet' => __('experiences.detail.v1.location.meeting_point'),
            'stop' => __('experiences.detail.v1.location.stop'),
        ],
    ];
@endphp

<section id="location" class="lv-xsection" aria-labelledby="location-title">
    <h2 id="location-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.location.title') }}</h2>
    <div class="lv-v1-location">
        @if($hasMap)
            <div class="lv-v1-map" x-data="experienceRouteMap(@js($mapConfig))" x-intersect.once.margin.200px="load()">
                <div x-ref="canvas" class="lv-v1-map__canvas" role="region"
                     aria-label="{{ $stopNames->count() > 1 ? $stopNames->implode(' → ') : __('experiences.detail.v1.location.route') }}"></div>
                @if($stopNames->count() > 1)
                    <ol class="lv-v1-map__legend" aria-hidden="true">
                        @foreach($stopNames as $name)
                            <li><span>{{ $loop->iteration }}</span>{{ $name }}</li>
                        @endforeach
                    </ol>
                @endif
            </div>
        @endif
        <div class="lv-v1-location__card">
            <h3 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.location.meet', ['place' => $location['place']]) }}</h3>
            @if($location['note'])<p class="lv-t-body-s lv-tone-muted">{{ $location['note'] }}</p>@endif
            @if($location['mapUrl'])
                <x-site.atoms.button :href="$location['mapUrl']" variant="outline" target="_blank" rel="noopener">{{ __('experiences.detail.v1.location.map') }}</x-site.atoms.button>
            @endif
        </div>
    </div>
</section>
