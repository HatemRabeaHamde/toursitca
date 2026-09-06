@php
    $reviews = $detail->reviewSummary;
    $score = (float) ($reviews['rating'] ?? 0);
@endphp

<section id="reviews" class="xp-detail-section">
    <h2 class="xp-section-heading xp-reviews-title">
        {{ __('ui.experience_detail.reviews_travelers_loved') }}
        <span class="xp-reviews-title-info" aria-label="{{ __('ui.experience_detail.reviews_info') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        </span>
    </h2>

    @if ($reviews['has_reviews'])
        <div class="xp-review-summary xp-review-summary--compact">
            <span class="xp-review-big-score">{{ $reviews['rating'] }}</span>
            <div>
                <div class="xp-review-stars-row" aria-label="{{ $reviews['rating'] }} {{ __('ui.experience_detail.out_of_5') }}">
                    @for ($s = 1; $s <= 5; $s++)
                        <svg class="xp-star{{ $s <= round($score) ? '' : ' xp-star--empty' }}" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </div>
                <p class="xp-review-label">{{ trans_choice('ui.messages.review_count', $reviews['count'], ['count' => number_format($reviews['count'])]) }}</p>
            </div>
        </div>

        @if ($detail->reviews->isNotEmpty())
            <div class="xp-review-cards" x-data="{ showAll: false }">
                <div class="xp-review-cards-grid">
                    @foreach ($detail->reviews->take(4) as $review)
                        <article class="xp-review-card">
                            <div class="xp-rc-stars" aria-label="{{ $review['rating'] }}/5">
                                @for ($s = 1; $s <= 5; $s++)
                                    <svg class="xp-star{{ $s <= $review['rating'] ? '' : ' xp-star--empty' }}" viewBox="0 0 24 24" fill="currentColor" width="14" height="14" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @endfor
                            </div>
                            <div class="xp-rc-header">
                                <div class="xp-rc-avatar" style="background: {{ $review['color'] }}" aria-hidden="true">{{ $review['initials'] }}</div>
                                <div class="xp-rc-meta">
                                    <strong class="xp-rc-name">{{ $review['name'] }}</strong>
                                    <span class="xp-rc-date">
                                        {{ $review['date'] }}
                                        @if ($review['is_verified'])
                                            · <span class="xp-rc-verified">{{ __('ui.experience_detail.verified_booking') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <p class="xp-rc-body">{{ $review['body'] }}</p>
                        </article>
                    @endforeach
                </div>
                @if ($detail->reviews->count() > 4)
                    <div class="xp-reviews-more">
                        <button type="button" class="xp-reviews-more-btn" @click="showAll = true">
                            {{ __('ui.experience_detail.see_more_reviews') }}
                        </button>
                    </div>
                @endif
            </div>
        @endif
    @else
        <p class="xp-no-reviews">{{ __('ui.experience_detail.no_reviews_yet') }}</p>
    @endif
</section>
