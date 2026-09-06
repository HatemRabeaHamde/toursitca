@extends('layouts.site')

@section('hide_site_chrome', 'true')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
    @vite(['resources/css/landing.css', 'resources/css/experience-card.css'])
@endpush

@section('content')
    @include('site.partials.landing-nav')

    <section class="hero" id="hero">
        <div class="hero-bg">
            <img class="hero-slide" src="https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=1800&q=90" alt="{{ __('ui.landing.hero_image_alt') }}">
            <img class="hero-slide" src="https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=1800&q=90" alt="{{ __('ui.landing.hero_image_alt') }}">
            <img class="hero-slide" src="https://images.unsplash.com/photo-1548013146-72479768bada?w=1800&q=90" alt="{{ __('ui.landing.hero_image_alt') }}">
            <img class="hero-slide" src="https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=1800&q=90" alt="{{ __('ui.landing.hero_image_alt') }}">
            <div class="hero-veil"></div>
        </div>

        <div class="hero-body">
            <p class="eyebrow-w">{{ __('ui.landing.eyebrow') }}</p>
            {{-- Raw output: lang string contains intentional <br>/<em> markup for the headline emphasis styling. --}}
            <h1 class="hero-h1 hero-typewriter">{!! __('ui.landing.hero_title') !!}</h1>
            <p class="hero-desc">{{ __('ui.landing.hero_description', ['count' => number_format($landing->stats['experiences_count'])]) }}</p>

            <div class="hero-pills" aria-label="{{ __('ui.landing.stats_label') }}">
                <div class="hp"><span class="hp-n">{{ number_format($landing->stats['experiences_count']) }}</span><p class="hp-l">{{ __('ui.landing.stats.experiences') }}</p></div>
                <div class="hp-div"></div>
                <div class="hp"><span class="hp-n">{{ number_format($landing->stats['cities_count']) }}</span><p class="hp-l">{{ __('ui.landing.stats.cities') }}</p></div>
                <div class="hp-div"></div>
                <div class="hp"><span class="hp-n">{{ $landing->stats['avg_rating'] }}★</span><p class="hp-l">{{ __('ui.landing.stats.avg_rating') }}</p></div>
                <div class="hp-div"></div>
                <div class="hp"><span class="hp-n">{{ number_format($landing->stats['happy_travelers']) }}</span><p class="hp-l">{{ __('ui.landing.stats.travelers') }}</p></div>
            </div>
        </div>
        @include('site.home.partials.hero-search')

    </section>

    <section class="trending-section" x-data="{ tab: 'trending' }">
        <div class="sec-head">
            <div>
                <p class="sec-label">{{ __('ui.landing.sections.trending_label') }}</p>
                <h2 class="sec-h2">{{ __('ui.landing.sections.trending_title') }}</h2>
            </div>
            <a class="see-all" href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}">
                {{ __('ui.landing.sections.browse_all_plain') }}
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div class="trend-tabs" role="tablist" aria-label="{{ __('ui.fields.sort') }}">
            <button type="button" class="ttab" role="tab" @click="tab = 'trending'" :aria-current="(tab === 'trending').toString()">{{ __('ui.landing.tabs.trending') }}</button>
            <button type="button" class="ttab" role="tab" @click="tab = 'newest'" :aria-current="(tab === 'newest').toString()">{{ __('ui.landing.tabs.newest') }}</button>
            <button type="button" class="ttab" role="tab" @click="tab = 'top_rated'" :aria-current="(tab === 'top_rated').toString()">{{ __('ui.landing.tabs.top_rated') }}</button>
        </div>

        @foreach ([
            'trending'  => $landing->trendingCards,
            'newest'    => $landing->newestCards,
            'top_rated' => $landing->topRatedCards,
        ] as $tabKey => $cards)
            <div x-show="tab === '{{ $tabKey }}'" @if ($tabKey !== 'trending') x-cloak @endif>
                @if ($cards->isEmpty())
                    <div class="trend-empty">{{ __('ui.messages.no_experiences') }}</div>
                @else
                    <div
                        class="carousel-shell"
                        x-data="homeCarousel({ autoplay: @js($cards->count() > 4) })"
                        x-on:mouseenter="pause()"
                        x-on:mouseleave="resume()"
                        x-on:focusin="pause()"
                        x-on:focusout="resume()"
                        x-on:touchstart.passive="pause()"
                    >
                        <div class="trend-row carousel-track" x-ref="track">
                            @foreach ($cards as $card)
                                @include('site.experiences.partials.card-grid', ['card' => $card])
                            @endforeach
                        </div>
                        @if ($cards->count() > 4)
                            <div class="carousel-actions">
                                <button class="carousel-btn" type="button" x-on:click="previous()" aria-label="{{ __('ui.landing.carousel.previous_experiences') }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                </button>
                                <button class="carousel-btn" type="button" x-on:click="next()" aria-label="{{ __('ui.landing.carousel.next_experiences') }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach

        <div class="trend-footer">
            <a class="trend-viewall" href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}">
                {{ __('ui.landing.sections.browse_all', ['count' => number_format($landing->stats['experiences_count'])]) }}
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </section>

    @include('site.home.partials.sections', ['landing' => $landing])
    @include('site.partials.footer')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nav = document.querySelector('[data-landing-nav]');
            if (!nav) return;
            const solid = nav.dataset.solid === 'true';
            const onScroll = () => nav.classList.toggle('scrolled', solid || window.scrollY > 30);
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        });
    </script>
@endpush
