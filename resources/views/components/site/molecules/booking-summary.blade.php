@props(['price', 'rating' => null, 'reviews' => 0])

<div class="lv-booking-summary" data-booking-summary>
    <div class="lv-booking-summary__top">
        <div class="lv-booking-summary__price">
            <p class="flex flex-wrap items-baseline gap-x-1.5">
                <span class="lv-t-caption lv-tone-muted">{{ __('experiences.detail.booking.from') }}</span>
                <span class="lv-bcard__amount" x-text="money(unitPrice())">{{ $price }}</span>
            </p>
            <span class="lv-t-caption lv-tone-muted" x-show="!isFlat()">{{ __('experiences.detail.booking.per_person') }}</span>
            <span class="lv-t-caption lv-tone-muted" x-show="isFlat()" x-cloak>{{ __('booking_flow.card.per_group') }}</span>
        </div>
        <span class="lv-booking-summary__type lv-t-label-s" aria-live="polite">
            <x-site.atoms.icon name="user-terracotta" :size="16" />
            <span x-text="isPrivateTrip() ? @js(__('experiences.detail.v1.booking.private')) : @js(__('booking_flow.card.shared_group'))">{{ __('booking_flow.card.shared_group') }}</span>
        </span>
    </div>
    @if($rating !== null && $reviews > 0)
        <a href="#reviews" class="lv-booking-summary__reviews" data-booking-reviews>
            <span class="flex items-center gap-1 lv-t-label-m lv-tone-terracotta">
                {{ $rating }}<x-site.atoms.icon name="star" :size="15" />
            </span>
            <span class="lv-t-caption lv-tone-muted underline underline-offset-2">{{ trans_choice('booking_flow.card.reviews', $reviews, ['count' => $reviews]) }}</span>
        </a>
    @endif
</div>
