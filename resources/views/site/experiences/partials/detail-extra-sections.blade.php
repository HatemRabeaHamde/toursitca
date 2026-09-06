@php
    $agency   = $detail->agencySummary;
    $initials = mb_strtoupper(mb_substr($agency['name'], 0, 2));
    $reviews  = $detail->reviewSummary;
    $score    = (float) ($reviews['rating'] ?? 0);
    $bars = [
        5 => min(100, max(0, (int) round(($score - 3) / 2 * 100))),
        4 => min(100, max(0, (int) round((5 - $score) / 2 * 80))),
        3 => min(100, max(0, (int) round((5 - $score) / 2 * 40))),
        2 => 5,
        1 => 2,
    ];
@endphp

{{-- ── Activity provider ────────────────────────────────────── --}}
<section id="provider" class="xp-card">
    <h2 class="xp-card-title">{{ __('ui.experience_detail.agency_title') }}</h2>

    <div class="xp-agency-card">
        <div class="xp-agency-avatar" aria-hidden="true">{{ $initials }}</div>

        <div class="xp-agency-info">
            <h3>{{ $agency['name'] }}</h3>
            @if (!empty($agency['city']))
                <p>📍 {{ $agency['city'] }}</p>
            @endif
            @if ($agency['is_verified'])
                <span class="xp-verified-badge">
                    ✓ {{ __('ui.experience_detail.verified_provider') }}
                </span>
            @endif
        </div>
    </div>
</section>

{{-- ── Reviews — GetYourGuide style ────────────────────────── --}}
<section id="reviews" class="xp-card">
    <h2 class="xp-card-title xp-reviews-title">
        {{ __('ui.experience_detail.reviews_travelers_loved') }}
        <span class="xp-reviews-title-info" aria-label="{{ __('ui.experience_detail.reviews_info') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        </span>
    </h2>

    @if ($reviews['has_reviews'])

        {{-- Summary row --}}
        <div class="xp-review-summary">
            <div class="xp-review-score-block">
                <span class="xp-review-big-score">{{ $reviews['rating'] }}</span>
                <div class="xp-review-stars-row" aria-label="{{ $reviews['rating'] }} {{ __('ui.experience_detail.out_of_5') }}">
                    @for ($s = 1; $s <= 5; $s++)
                        <svg class="xp-star{{ $s <= round($score) ? '' : ' xp-star--empty' }}" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </div>
                <p class="xp-review-label">
                    {{ trans_choice('ui.messages.review_count', $reviews['count'], ['count' => number_format($reviews['count'])]) }}
                </p>
            </div>

            <div class="xp-review-bars" aria-hidden="true">
                @foreach ($bars as $star => $pct)
                    <div class="xp-review-bar-row">
                        <span>{{ $star }}</span>
                        <div class="xp-review-bar-track">
                            <div class="xp-review-bar-fill" style="width: {{ $pct }}%"></div>
                        </div>
                        <span>{{ $pct }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Review cards grid --}}
        @if ($detail->reviews->isNotEmpty())
            <div class="xp-review-cards" x-data="{ showAll: false }">
                <div class="xp-review-cards-grid">
                    @foreach ($detail->reviews->take(4) as $review)
                        <div class="xp-review-card">
                            {{-- Header: avatar + name + date --}}
                            <div class="xp-rc-header">
                                <div class="xp-rc-avatar" style="background: {{ $review['color'] }}" aria-hidden="true">{{ $review['initials'] }}</div>
                                <div class="xp-rc-meta">
                                    <strong class="xp-rc-name">{{ $review['name'] }}</strong>
                                    <span class="xp-rc-date">
                                        {{ $review['date'] }}
                                        @if ($review['is_verified'])
                                            &nbsp;·&nbsp;<span class="xp-rc-verified">{{ __('ui.experience_detail.verified_booking') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Stars --}}
                            <div class="xp-rc-stars" aria-label="{{ $review['rating'] }}/5">
                                @for ($s = 1; $s <= 5; $s++)
                                    <svg class="xp-star{{ $s <= $review['rating'] ? '' : ' xp-star--empty' }}" viewBox="0 0 24 24" fill="currentColor" width="14" height="14" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @endfor
                            </div>

                            {{-- Body --}}
                            <p class="xp-rc-body">{{ $review['body'] }}</p>

                            @if ($review['agency_reply'])
                                <div class="xp-rc-reply">
                                    <strong>{{ __('ui.experience_detail.agency_reply') }}</strong>
                                    <p>{{ $review['agency_reply'] }}</p>
                                    @if ($review['replied_at'])
                                        <span class="xp-rc-reply-date">{{ $review['replied_at'] }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($detail->reviews->count() > 4)
                    <div class="xp-reviews-more" x-show="! showAll" x-cloak>
                        <button type="button" class="xp-reviews-more-btn" @click="showAll = true">
                            {{ __('ui.experience_detail.see_more_reviews') }}
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                @endif
            </div>
        @endif

    @else
        <p class="xp-no-reviews">{{ __('ui.experience_detail.no_reviews_yet') }}</p>
    @endif
</section>

{{-- ── Important information (accordion) ──────────────────── --}}
<section id="important" class="xp-card">
    <h2 class="xp-card-title">{{ __('ui.experience_detail.important_info_title') }}</h2>

    <div class="xp-accordion">
        @foreach (__('ui.experience_detail.important_items') as $index => $item)
            <div class="xp-accordion-item" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                <button
                    type="button"
                    class="xp-accordion-trigger"
                    @click="open = !open"
                    :aria-expanded="open"
                >
                    <span>{{ $item['title'] }}</span>
                    <span class="xp-accordion-chevron" :class="{ 'is-open': open }" aria-hidden="true">▾</span>
                </button>
                <div class="xp-accordion-body" x-show="open" x-cloak x-transition>
                    {{ $item['body'] }}
                </div>
            </div>
        @endforeach
    </div>
</section>
