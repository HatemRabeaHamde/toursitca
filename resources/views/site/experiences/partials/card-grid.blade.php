@php
    $filledStars = (int) round((float) $card->ratingAvg);

    $discount = null;

    if ($card->originalPrice !== null) {
        $price = (float) str_replace(',', '', $card->priceFrom);
        $original = (float) str_replace(',', '', $card->originalPrice);

        if ($original > 0) {
            $discount = max(0, (int) round((1 - ($price / $original)) * 100));
        }
    }
@endphp

<article class="xc">
    <div class="xc-img">
        <img src="{{ $card->thumbnailUrl ?: 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=800&q=80' }}" alt="{{ $card->title }}" loading="lazy">

        @if ($card->badges)
            <span class="xc-badge">{{ $card->badges[0] }}</span>
        @endif

        @if ($card->availabilityStatus !== 'sold_out')
            <span class="xc-avail">
                <span class="xc-avail-dot {{ $card->availabilityStatus === 'few_spots' ? 'amber' : 'green' }}"></span>
                @if ($card->availabilityStatus === 'few_spots')
                    {{ trans_choice('ui.labels.spots_left', $card->spotsLeft, ['count' => $card->spotsLeft]) }}
                @else
                    {{ __('ui.labels.available') }}
                @endif
            </span>
        @endif

        <span class="xc-dur">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $card->durationLabel }}
        </span>

        {{-- Wishlist is a V2 feature; this is a visual affordance only for now. --}}
        <button type="button" class="xc-wish" aria-label="{{ __('ui.labels.save') }}"
                x-data="{ saved: false }" :class="{ 'is-saved': saved }" @click="saved = ! saved">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
    </div>

    <a class="xc-body" href="{{ $card->showUrl }}" aria-label="{{ $card->title }}">
        <p class="xc-city">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $card->city }}
        </p>
        <h3 class="xc-title">{{ $card->title }}</h3>

        @if ($card->agencyName)
            <p class="xc-host">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ __('ui.labels.hosted_by', ['agency' => $card->agencyName]) }}
            </p>
        @endif

        @if ($card->tags)
            <div class="xc-tags" @if (count($card->tags) > 2) x-data="{ expanded: false }" @endif>
                @foreach (array_slice($card->tags, 0, 2) as $tag)
                    <span class="xc-tag">{{ $tag }}</span>
                @endforeach

                @if (count($card->tags) > 2)
                    <template x-if="! expanded">
                        <button type="button" class="xc-tag xc-tag-more" @click.stop.prevent="expanded = true">+{{ count($card->tags) - 2 }}</button>
                    </template>
                    <template x-if="expanded">
                        @foreach (array_slice($card->tags, 2) as $tag)
                            <span class="xc-tag">{{ $tag }}</span>
                        @endforeach
                    </template>
                @endif
            </div>
        @endif

        @if ($card->reviewsCount > 0)
            <div class="xc-rating">
                <span class="xc-stars" aria-hidden="true">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="{{ $i <= $filledStars ? '' : 'is-empty' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </span>
                <span class="xc-score">{{ $card->ratingAvg }}</span>
                <span class="xc-cnt">({{ number_format($card->reviewsCount) }} {{ __('ui.labels.reviews') }})</span>
            </div>
        @endif

        @if ($card->isDeal && $card->dealEndsAt)
            <p class="xc-deal">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ __('ui.labels.deal_ends', ['time' => $card->dealEndsAt->diffForHumans(['parts' => 1])]) }}
            </p>
        @endif

        <div class="xc-foot">
            <div class="xc-price">
                @if ($discount)
                    <span class="xc-orig">{{ $card->originalPrice }} {{ $card->currency }}</span>
                @endif
                <span class="xc-price-row">
                    <span class="xc-price-lbl">{{ __('ui.labels.from') }}</span>
                    <span class="xc-amount">{{ $card->priceFrom }}</span>
                    <span class="xc-cur">{{ $card->currency }}</span>
                    @if ($discount)
                        <span class="xc-disc">−{{ $discount }}%</span>
                    @endif
                </span>
                <span class="xc-pp">{{ __('ui.labels.per_person') }}</span>
            </div>

            <span class="xc-btn">{{ __('ui.landing.view_experience') }}</span>
        </div>
    </a>
</article>
