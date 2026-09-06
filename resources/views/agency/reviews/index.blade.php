@extends('layouts.agency')

@section('title', __('ui.nav.reviews'))

@section('page-title', __('ui.nav.reviews'))

@section('content')

    {{-- ── Rating summary ──────────────────────────────────────── --}}
    @if ($ratingCount > 0)
        <div class="ag-card mb-5">
            <div class="ag-rating-summary">
                <div class="ag-rating-score">{{ number_format($ratingAvg, 1) }}</div>
                <div class="ag-rating-meta">
                    <div class="ag-rating-stars" aria-label="{{ number_format($ratingAvg, 1) }} stars">
                        @for ($i = 1; $i <= 5; $i++)
                            @php $fill = min(1, max(0, $ratingAvg - ($i - 1))); @endphp
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $fill >= 0.5 ? 'var(--clay)' : 'none' }}" stroke="var(--clay)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        @endfor
                    </div>
                    <div class="ag-rating-count">
                        {{ trans_choice('ui.messages.review_count', $ratingCount, ['count' => $ratingCount]) }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Review list ─────────────────────────────────────────── --}}
    <x-ui.card :title="__('ui.nav.reviews')">
        @if ($reviews->isEmpty())
            <x-ui.empty-state :title="__('ui.messages.no_reviews')" />
        @else
            <ul class="ag-review-list">
                @foreach ($reviews as $review)
                    @php
                        $expTitle = $review->experience?->getTranslation('title', app()->getLocale(), false) ?? '—';
                        $initials = collect(explode(' ', $review->user?->name ?? '?'))
                            ->take(2)->map(fn ($w) => strtoupper($w[0] ?? ''))->implode('');
                    @endphp
                    <li class="ag-review-row">
                        <div class="ag-avatar ag-avatar--{{ $loop->index % 6 }}" aria-hidden="true">{{ $initials }}</div>
                        <div class="ag-review-body">
                            <div class="ag-review-top">
                                <span class="ag-review-author">{{ $review->user?->name ?? '—' }}</span>
                                <div class="ag-review-stars" aria-label="{{ $review->rating }} stars">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="{{ $s <= $review->rating ? 'var(--clay)' : 'none' }}" stroke="var(--clay)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="ag-review-date">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="ag-review-exp">{{ $expTitle }}</div>
                            @if ($review->body)
                                <p class="ag-review-text">{{ $review->body }}</p>
                            @endif
                        </div>
                        @if (! $review->is_visible)
                            <x-ui.badge color="slate">Hidden</x-ui.badge>
                        @endif
                    </li>
                @endforeach
            </ul>

            <x-ui.pagination :paginator="$reviews" />
        @endif
    </x-ui.card>

@endsection
