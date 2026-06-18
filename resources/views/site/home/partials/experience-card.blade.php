@php
    $filledStars = (int) floor((float) $card->ratingAvg);
    $stars = str_repeat('★', $filledStars).str_repeat('☆', 5 - $filledStars);

    $discount = null;

    if ($card->originalPrice !== null) {
        $price = (float) str_replace(',', '', $card->priceFrom);
        $original = (float) str_replace(',', '', $card->originalPrice);

        if ($original > 0) {
            $discount = max(0, (int) round((1 - ($price / $original)) * 100));
        }
    }
@endphp

<a class="tc" href="{{ $card->showUrl }}" aria-label="{{ $card->title }}">
    <div class="tc-img">
        <img src="{{ $card->thumbnailUrl ?: 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=700&q=80' }}" alt="{{ $card->title }}" loading="lazy">

        @if (in_array(__('ui.badges.top_rated'), $card->badges, true))
            <span class="tc-badge tcb-top">{{ __('ui.badges.top_rated') }}</span>
        @endif

        <div class="tc-dur">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $card->durationLabel }}
        </div>
    </div>

    <div class="tc-body">
        <p class="tc-city">{{ $card->city }} · {{ $card->category }}</p>
        <h3 class="tc-title">{{ $card->title }}</h3>

        @if ($card->reviewsCount > 0)
            <div class="tc-rating">
                <span class="tc-stars">{{ $stars }}</span>
                <span class="tc-score">{{ $card->ratingAvg }}</span>
                <span class="tc-cnt">({{ number_format($card->reviewsCount) }})</span>
            </div>
        @endif
    </div>

    <div class="tc-foot">
        <div>
            <p class="tc-price-lbl">{{ __('ui.labels.from') }}</p>
            <div class="tc-price-row">
                <span class="tc-amount">{{ $card->priceFrom }}</span>
                <span class="tc-cur">{{ $card->currency }}</span>
                @if ($card->originalPrice)
                    <span class="tc-orig">{{ $card->originalPrice }}</span>
                @endif
                @if ($discount)
                    <span class="tc-disc">−{{ $discount }}%</span>
                @endif
            </div>
        </div>

        <span class="tc-btn">{{ __('ui.landing.view_experience') }}</span>
    </div>
</a>
