@extends('layouts.site')

@section('hide_site_chrome', 'true')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
    @vite('resources/css/landing.css')
@endpush

@php
    $locale = app()->getLocale();
    $moods = [
        [
            'key' => 'taste',
            'label' => __('ui.landing.compass.moods.taste.label'),
            'summary' => __('ui.landing.compass.moods.taste.summary'),
            'image' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=1800&q=88',
            'url' => route('site.experiences.index', ['locale' => $locale, 'category' => 'food']),
        ],
        [
            'key' => 'desert',
            'label' => __('ui.landing.compass.moods.desert.label'),
            'summary' => __('ui.landing.compass.moods.desert.summary'),
            'image' => 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=1800&q=88',
            'url' => route('site.experiences.index', ['locale' => $locale, 'category' => 'desert']),
        ],
        [
            'key' => 'culture',
            'label' => __('ui.landing.compass.moods.culture.label'),
            'summary' => __('ui.landing.compass.moods.culture.summary'),
            'image' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=1800&q=88',
            'url' => route('site.experiences.index', ['locale' => $locale, 'category' => 'culture']),
        ],
        [
            'key' => 'coast',
            'label' => __('ui.landing.compass.moods.coast.label'),
            'summary' => __('ui.landing.compass.moods.coast.summary'),
            'image' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=1800&q=88',
            'url' => route('site.experiences.index', ['locale' => $locale, 'category' => 'water']),
        ],
        [
            'key' => 'mountains',
            'label' => __('ui.landing.compass.moods.mountains.label'),
            'summary' => __('ui.landing.compass.moods.mountains.summary'),
            'image' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=1800&q=88',
            'url' => route('site.experiences.index', ['locale' => $locale, 'category' => 'adventure']),
        ],
        [
            'key' => 'slow',
            'label' => __('ui.landing.compass.moods.slow.label'),
            'summary' => __('ui.landing.compass.moods.slow.summary'),
            'image' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1800&q=88',
            'url' => route('site.experiences.index', ['locale' => $locale]),
        ],
    ];

    $storyStops = [
        ['time' => __('ui.landing.compass.story.morning.time'), 'title' => __('ui.landing.compass.story.morning.title'), 'body' => __('ui.landing.compass.story.morning.body')],
        ['time' => __('ui.landing.compass.story.midday.time'), 'title' => __('ui.landing.compass.story.midday.title'), 'body' => __('ui.landing.compass.story.midday.body')],
        ['time' => __('ui.landing.compass.story.sunset.time'), 'title' => __('ui.landing.compass.story.sunset.title'), 'body' => __('ui.landing.compass.story.sunset.body')],
    ];

    $confidenceItems = [
        ['title' => __('ui.landing.compass.confidence.meeting.title'), 'body' => __('ui.landing.compass.confidence.meeting.body')],
        ['title' => __('ui.landing.compass.confidence.agencies.title'), 'body' => __('ui.landing.compass.confidence.agencies.body')],
        ['title' => __('ui.landing.compass.confidence.cancellation.title'), 'body' => __('ui.landing.compass.confidence.cancellation.body')],
    ];
@endphp

@section('content')
    @include('site.partials.landing-nav', ['solid' => true])

    <section
        class="compass-hero"
        x-data="{ active: 0, moods: @js($moods) }"
    >
        <div class="compass-bg">
            @foreach ($moods as $index => $mood)
                <img
                    src="{{ $mood['image'] }}"
                    alt="{{ $mood['label'] }}"
                    @class(['is-active' => $index === 0])
                    :class="{ 'is-active': active === {{ $index }} }"
                >
            @endforeach
            <div class="compass-veil"></div>
        </div>

        <div class="compass-shell">
            <div class="compass-copy">
                <p class="compass-kicker">{{ __('ui.landing.compass.kicker') }}</p>
                <h1 class="compass-title compass-typewriter">{{ __('ui.landing.compass.title') }}</h1>
                <p class="compass-lede">{{ __('ui.landing.compass.lede') }}</p>

                <div class="compass-proof" aria-label="{{ __('ui.landing.stats_label') }}">
                    <span>{{ number_format($landing->stats['experiences_count']) }} {{ __('ui.landing.stats.experiences') }}</span>
                    <span>{{ number_format($landing->stats['cities_count']) }} {{ __('ui.landing.stats.cities') }}</span>
                    <span>{{ $landing->stats['avg_rating'] }}★ {{ __('ui.landing.stats.avg_rating') }}</span>
                </div>
            </div>

            <div class="compass-panel">
                <p class="compass-panel-label">{{ __('ui.landing.compass.question') }}</p>
                <div class="compass-dial" role="listbox" aria-label="{{ __('ui.landing.compass.question') }}">
                    <div class="compass-center">
                        <span x-text="moods[active].label"></span>
                        <small x-text="moods[active].summary"></small>
                    </div>
                    @foreach ($moods as $index => $mood)
                        <button
                            type="button"
                            class="compass-choice compass-choice-{{ $index + 1 }}"
                            :class="{ 'is-active': active === {{ $index }} }"
                            @click="active = {{ $index }}"
                        >
                            {{ $mood['label'] }}
                        </button>
                    @endforeach
                </div>

                <a class="compass-cta" :href="moods[active].url">
                    {{ __('ui.landing.compass.cta') }}
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
    </section>

    <section class="compass-story">
        <div class="compass-section-head">
            <p class="sec-label">{{ __('ui.landing.compass.story_label') }}</p>
            <h2 class="sec-h2">{{ __('ui.landing.compass.story_title') }}</h2>
        </div>

        <div class="compass-story-grid">
            @foreach ($storyStops as $stop)
                <article class="compass-story-card">
                    <span>{{ $stop['time'] }}</span>
                    <h3>{{ $stop['title'] }}</h3>
                    <p>{{ $stop['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="compass-confidence">
        <div>
            <p class="sec-label">{{ __('ui.landing.compass.confidence_label') }}</p>
            <h2 class="sec-h2">{{ __('ui.landing.compass.confidence_title') }}</h2>
        </div>
        <div class="compass-confidence-grid">
            @foreach ($confidenceItems as $item)
                <article>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    @if ($landing->trendingCards->isNotEmpty())
        <section class="compass-starts">
            <div class="compass-section-head">
                <p class="sec-label">{{ __('ui.landing.compass.starts_label') }}</p>
                <h2 class="sec-h2">{{ __('ui.landing.compass.starts_title') }}</h2>
            </div>

            <div class="compass-card-row">
                @foreach ($landing->trendingCards->take(4) as $card)
                    @include('site.home.partials.experience-card', ['card' => $card])
                @endforeach
            </div>
        </section>
    @endif

    @include('site.partials.footer')
@endsection
