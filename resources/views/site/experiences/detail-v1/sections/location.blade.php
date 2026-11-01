{{-- Schematic route (real stop coordinates) + where the trip starts. --}}
@php($location = $page->location)

<section id="location" class="lv-xsection" aria-labelledby="location-title">
    <h2 id="location-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('experiences.detail.v1.location.title') }}</h2>
    <div @class(['lv-v1-location', 'lv-v1-location--solo' => ! $location['stops']])>
        @if($location['stops'])
            <svg class="lv-v1-map" viewBox="0 0 470 270" role="img" aria-label="{{ collect($location['stops'])->pluck('name')->implode(' → ') }}">
                <rect width="470" height="270" fill="#f4f0e8" />
                <ellipse cx="170" cy="40" rx="130" ry="100" fill="#ece4d5" />
                <ellipse cx="410" cy="250" rx="150" ry="110" fill="#ece4d5" />
                <polyline points="{{ collect($location['stops'])->map(fn ($stop) => $stop['x'].','.$stop['y'])->implode(' ') }}" fill="none" stroke="#b9502b" stroke-width="2" stroke-dasharray="6 6" />
                @foreach($location['stops'] as $stop)
                    <circle cx="{{ $stop['x'] }}" cy="{{ $stop['y'] }}" r="7" fill="#1b2b26" stroke="#fff" stroke-width="2" />
                    <text x="{{ $stop['x'] + ($stop['x'] > 300 ? -10 : 14) }}" y="{{ $stop['y'] + ($stop['x'] > 300 ? 28 : -8) }}" text-anchor="{{ $stop['x'] > 300 ? 'end' : 'start' }}" class="lv-v1-map__label">{{ $stop['name'] }}</text>
                @endforeach
            </svg>
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
