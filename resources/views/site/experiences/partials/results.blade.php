{{-- Results bar + grid + pagination. Re-rendered in full on filter changes
     (AJAX swap target via #xp-results) and on normal full-page loads. --}}
@php
    $indexUrl ??= route('site.experiences.index', ['locale' => app()->getLocale()]);
    $current ??= array_filter(
        request()->except('page'),
        fn ($v) => $v !== null && $v !== '' && $v !== '0' && $v !== [],
    );
@endphp
<div class="xp-results-bar">
    <p class="xp-rb-count">{!! trans_choice('ui.experiences_page.found', $experiences->total(), [
        'count' => '<strong>'.number_format($experiences->total()).'</strong>',
    ]) !!}</p>
    <div class="xp-rb-tabs">
        @php
            $tabs = [
                'rating' => 'tab_top',
                'recommended' => 'tab_trending',
                'newest' => 'tab_new',
            ];
        @endphp
        @foreach ($tabs as $sortKey => $labelKey)
            <a class="xp-rb-tab {{ $filters->sort === $sortKey ? 'is-active' : '' }}"
               href="{{ $indexUrl }}?{{ http_build_query(array_merge($current, ['sort' => $sortKey, 'page' => null])) }}">
                {{ __('ui.experiences_page.'.$labelKey) }}
            </a>
        @endforeach
    </div>
</div>

@if ($cards->isEmpty())
    <div class="xp-grid" :class="{ 'is-list': view === 'list' }">
        <div class="xp-empty">{{ __('ui.messages.no_experiences') }}</div>
    </div>
@else
    <div class="xp-grid" :class="{ 'is-list': view === 'list' }">
        @foreach ($cards as $card)
            @include('site.experiences.partials.card-grid', ['card' => $card])
        @endforeach
    </div>

    @if ($experiences->hasMorePages())
        <div class="xp-pagination">
            <button type="button" class="xp-load-more" data-next-url="{{ $experiences->nextPageUrl() }}">
                {{ __('ui.experiences_page.load_more') }}
            </button>
        </div>
    @endif
@endif
