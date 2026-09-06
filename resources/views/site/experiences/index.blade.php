@extends('layouts.site')

@section('hide_site_chrome', 'true')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    @vite(['resources/css/landing.css', 'resources/css/experience-card.css', 'resources/css/experiences.css'])
@endpush

@php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $indexUrl = route('site.experiences.index', ['locale' => $locale]);
    $homeUrl = route('site.home', ['locale' => $locale]);

    $currency = config('payment.currency', 'MAD');
    $maxPrice = (int) config('booking.max_price_display', 500);

    $categoryLabel = fn (string $slug): string => Str::title(str_replace(['_', '-'], ' ', $slug));

    // Current query, minus pagination, used to build "carry" inputs and chip-remove links.
    $current = array_filter(
        request()->except('page'),
        fn ($v) => $v !== null && $v !== '' && $v !== '0' && $v !== [],
    );

    // A URL for the current query with one key (or one array value) removed.
    $removeUrl = function (string $key, ?string $value = null) use ($current, $indexUrl): string {
        $rest = $current;
        if ($value !== null && isset($rest[$key]) && is_array($rest[$key])) {
            $rest[$key] = array_values(array_filter($rest[$key], fn ($v) => $v !== $value));
            if ($rest[$key] === []) {
                unset($rest[$key]);
            }
        } else {
            unset($rest[$key]);
        }

        return $indexUrl.($rest ? '?'.http_build_query($rest) : '');
    };
    $filterUrl = function (array $overrides) use ($current, $indexUrl): string {
        $query = array_filter(
            array_merge($current, $overrides),
            fn ($v) => $v !== null && $v !== '' && $v !== [],
        );

        return $indexUrl.($query ? '?'.http_build_query($query) : '');
    };

    // Active-filter chips.
    $chips = [];
    $durationLabels = [
        'half' => __('ui.experiences_page.duration_half'),
        'full' => __('ui.experiences_page.duration_full'),
        'multi' => __('ui.experiences_page.duration_multi'),
    ];
    foreach ($filters->categories as $slug) {
        $chips[] = ['label' => $categoryLabel($slug), 'remove' => $removeUrl('category', $slug)];
    }
    if ($filters->duration && isset($durationLabels[$filters->duration])) {
        $chips[] = ['label' => $durationLabels[$filters->duration], 'remove' => $removeUrl('duration')];
    }
    if ($filters->search) {
        $chips[] = ['label' => '"'.$filters->search.'"', 'remove' => $removeUrl('search')];
    }
    if (request()->boolean('private_available')) {
        $chips[] = ['label' => __('ui.features.private_option_available'), 'remove' => $removeUrl('private_available')];
    }
    if (request()->boolean('pickup_available')) {
        $chips[] = ['label' => __('ui.features.pickup_available'), 'remove' => $removeUrl('pickup_available')];
    }

    $hasFilters = ! empty($chips)
        || $filters->minPrice
        || $filters->maxPrice
        || $filters->city
        || $filters->date
        || $filters->dateFrom
        || $filters->dateTo
        || $filters->participants;

    $popular = [
        'medina' => __('ui.experiences_page.popular.medina'),
        'desert' => __('ui.experiences_page.popular.desert'),
        'coast' => __('ui.experiences_page.popular.coast'),
    ];
@endphp

@section('content')
    @include('site.partials.xp-nav', [
        'searchNav' => $searchNav,
        'currentFilters' => [
            'search' => $filters->search ?? '',
            'city' => $filters->city ?? '',
            'date_from' => $filters->dateFrom ?? ($filters->date ?? ''),
            'date_to' => $filters->dateTo ?? '',
            'participants' => $filters->participants ?? 1,
        ],
    ])

    <div class="xp-page">

        {{-- ═══ HEADER STRIP ═══ --}}
        <div class="xp-header">
            <div class="xp-header-inner">
                <div>
                    <div class="xp-breadcrumb">
                        <a href="{{ $homeUrl }}">{{ __('ui.experiences_page.breadcrumb_home') }}</a>
                        <span>›</span>
                        <a href="{{ $indexUrl }}">{{ __('ui.experiences_page.breadcrumb_explore') }}</a>
                    </div>
                    <h1 class="xp-title">{{ __('ui.experiences_page.title_lead') }} <em>{{ __('ui.experiences_page.title_emphasis') }}</em></h1>
                    <p class="xp-subtitle">{{ __('ui.experiences_page.subtitle') }}</p>
                </div>
                <div class="xp-stats">
                    <div class="xp-stat">
                        <div class="xp-stat-num">{{ number_format($stats['experiences']) }}</div>
                        <div class="xp-stat-lbl">{!! __('ui.experiences_page.stat_experiences') !!}</div>
                    </div>
                    <div class="xp-stat">
                        <div class="xp-stat-num">{{ number_format($stats['destinations']) }}</div>
                        <div class="xp-stat-lbl">{!! __('ui.experiences_page.stat_destinations') !!}</div>
                    </div>
                    @if ($stats['rating'] > 0)
                        <div class="xp-stat">
                            <div class="xp-stat-num">{{ number_format($stats['rating'], 1) }}★</div>
                            <div class="xp-stat-lbl">{!! __('ui.experiences_page.stat_rating') !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- One GET form wraps the listing controls and sidebar filters. Search,
             city, date, and participants are submitted by xp-nav above; these
             hidden inputs preserve those values when sidebar filters change. --}}
        <form method="GET"
              action="{{ $indexUrl }}"
              data-experience-filters
              x-data="experienceFilters()"
              :aria-busy="loading.toString()"
              @submit.prevent="submit()">
            <input type="hidden" name="sort" value="{{ $filters->sort }}" x-ref="sort">
            @if ($filters->search)
                <input type="hidden" name="search" value="{{ $filters->search }}">
            @endif
            @if ($filters->city)
                <input type="hidden" name="city" value="{{ $filters->city }}">
            @endif
            @if ($filters->date)
                <input type="hidden" name="date" value="{{ $filters->date }}">
            @endif
            @if ($filters->dateFrom)
                <input type="hidden" name="date_from" value="{{ $filters->dateFrom }}">
            @endif
            @if ($filters->dateTo)
                <input type="hidden" name="date_to" value="{{ $filters->dateTo }}">
            @endif
            @if ($filters->participants)
                <input type="hidden" name="participants" value="{{ $filters->participants }}">
            @endif

            {{-- ═══ LISTING TOOLS ═══ --}}
            <div class="xp-listing-tools" style="top:0px">
                <div class="xp-listing-tools-inner">
                    <button type="button" class="xp-toggle-filters {{ $hasFilters ? 'has-filters' : '' }}"
                            :class="{ 'is-active': filters }" @click="filters = ! filters"
                            :aria-expanded="filters.toString()" aria-controls="xp-filters">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        {{ __('ui.experiences_page.filters') }}
                        <span class="xp-tf-dot"></span>
                    </button>

                    <div class="xp-sort">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <select aria-label="{{ __('ui.fields.sort') }}"
                                @change="$refs.sort.value = $event.target.value; submit()">
                            @foreach (['recommended', 'price_low', 'price_high', 'rating', 'newest'] as $sortKey)
                                <option value="{{ $sortKey }}" @selected($filters->sort === $sortKey)>{{ __('ui.sort.'.$sortKey) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <span class="xp-results">{!! __('ui.experiences_page.showing', [
                        'shown' => '<strong>'.number_format($experiences->count()).'</strong>',
                        'total' => number_format($experiences->total()),
                    ]) !!}</span>
                </div>

                @if ($chips)
                    <div class="xp-active-filters">
                        @foreach ($chips as $chip)
                            <span class="xp-af-chip">
                                {{ $chip['label'] }}
                                <a href="{{ $chip['remove'] }}" aria-label="{{ __('ui.experiences_page.clear') }}">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </a>
                            </span>
                        @endforeach
                        <a class="xp-af-clear" href="{{ $indexUrl }}">{{ __('ui.experiences_page.clear') }}</a>
                    </div>
                @endif
            </div>

            {{-- ═══ MAIN BODY ═══ --}}
            <div class="xp-body" :class="{ 'xp-filters-hidden': ! filters }">

                {{-- SIDEBAR --}}
                <aside class="xp-aside" id="xp-filters" x-show="filters" x-cloak style="top:85px"
                       x-transition:enter="xp-aside-enter"
                       x-transition:enter-start="xp-aside-enter-start">
                    <div class="xp-aside-head">
                        <h2>{{ __('ui.experiences_page.filters') }}</h2>
                        <a class="xp-reset" href="{{ $indexUrl }}">{{ __('ui.experiences_page.reset_all') }}</a>
                    </div>

                    @if ($categories->isNotEmpty())
                        <div class="xp-fgroup">
                            <div class="xp-fg-label">{{ __('ui.experiences_page.category') }}</div>
                            @foreach ($categories as $category => $total)
                                <label class="xp-opt">
                                    <span class="xp-opt-l">
                                        <input type="checkbox" name="category[]" value="{{ $category }}"
                                               @checked(in_array($category, $filters->categories, true)) @change="submit()">
                                        <span class="xp-opt-name">{{ Str::title(str_replace(['_', '-'], ' ', $category)) }}</span>
                                    </span>
                                    <span class="xp-opt-cnt">{{ number_format($total) }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="xp-divider"></div>
                    @endif

                    <div class="xp-fgroup" x-data="{ max: {{ (int) ($filters->maxPrice ?: $maxPrice) }} }">
                        <div class="xp-fg-label">{{ __('ui.experiences_page.price_range') }}</div>
                        <div class="xp-range-wrap">
                            <div class="xp-range-vals">
                                <span>{{ $filters->minPrice ? (int) $filters->minPrice : 0 }} {{ $currency }}</span>
                                <span x-text="(max >= {{ $maxPrice }} ? '{{ $maxPrice }}+' : max) + ' {{ $currency }}'"></span>
                            </div>
                            <input type="range" class="xp-range" min="0" max="{{ $maxPrice }}" step="10"
                                   x-model="max" @change="submit()">
                            <input type="hidden" name="max_price" :value="max" :disabled="Number(max) >= {{ $maxPrice }}">
                            <div class="xp-range-pills">
                                <a class="xp-range-pill" href="{{ $filterUrl(['max_price' => 50, 'page' => null]) }}">0–50</a>
                                <a class="xp-range-pill" href="{{ $filterUrl(['min_price' => 50, 'max_price' => 150, 'page' => null]) }}">50–150</a>
                                <a class="xp-range-pill" href="{{ $filterUrl(['min_price' => 150, 'max_price' => null, 'page' => null]) }}">150+</a>
                            </div>
                        </div>
                    </div>

                    <div class="xp-divider"></div>

                    <div class="xp-fgroup">
                        <div class="xp-fg-label">{{ __('ui.experiences_page.duration') }}</div>
                        @foreach (['half' => 'duration_half', 'full' => 'duration_full', 'multi' => 'duration_multi'] as $value => $labelKey)
                            <label class="xp-opt">
                                <span class="xp-opt-l">
                                    <input type="checkbox" name="duration" value="{{ $value }}"
                                           @checked($filters->duration === $value) @change="onlyOne($event); submit()">
                                    <span class="xp-opt-name">{{ __('ui.experiences_page.'.$labelKey) }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="xp-divider"></div>

                    <div class="xp-fgroup">
                        <div class="xp-fg-label">{{ __('ui.experiences_page.options') }}</div>
                        <label class="xp-opt">
                            <span class="xp-opt-l">
                                <input type="checkbox" name="private_available" value="1" @checked(request()->boolean('private_available')) @change="submit()">
                                <span class="xp-opt-name">{{ __('ui.features.private_option_available') }}</span>
                            </span>
                        </label>
                        <label class="xp-opt">
                            <span class="xp-opt-l">
                                <input type="checkbox" name="pickup_available" value="1" @checked(request()->boolean('pickup_available')) @change="submit()">
                                <span class="xp-opt-name">{{ __('ui.features.pickup_available') }}</span>
                            </span>
                        </label>
                    </div>

                    {{-- Fallback for no-JS only; Alpine removes it once running
                         (every control auto-submits, so it's redundant with JS). --}}
                    <button type="submit" class="xp-apply" x-init="$el.remove()">{{ __('ui.experiences_page.apply') }}</button>
                </aside>

                {{-- RESULTS --}}
                <main id="xp-results" :class="{ 'xp-loading': loading }">
                    @include('site.experiences.partials.results')
                </main>
            </div>
        </form>

        @if ($city && $landmarks->isNotEmpty())
            <section class="xp-section">
                <h2 class="xp-section-title">{{ __('ui.experiences_page.top_sights', ['city' => $city]) }}</h2>
                <div class="xp-sights">
                    @foreach ($landmarks as $landmark)
                        <div class="xs-card">
                            <div class="xs-img">
                                <img src="{{ $landmark['image_url'] }}" alt="{{ $landmark['name'] }}" loading="lazy">
                            </div>
                            <h3 class="xs-title">{{ $landmark['name'] }}</h3>
                            <p class="xs-count">{{ trans_choice('ui.labels.activities_count', $landmark['activities_count'], ['count' => $landmark['activities_count']]) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($city && $beyondCards->isNotEmpty())
            <section class="xp-section">
                <h2 class="xp-section-title">{{ __('ui.experiences_page.go_beyond', ['city' => $city]) }}</h2>
                <div class="xp-grid">
                    @foreach ($beyondCards as $card)
                        @include('site.experiences.partials.card-grid', ['card' => $card])
                    @endforeach
                </div>
            </section>
        @endif

        @include('site.experiences.partials.cta')
    </div>

    @include('site.partials.footer')
@endsection
