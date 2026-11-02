{{-- Phone / tablet bar: the next missing step (or the running total). Opens the booking sheet until the trip is configured, then requests it. Lives inside the booking scope. --}}
<div class="lv-xbar">
    <button type="button" class="flex min-w-0 flex-col text-start" @click="sheetOpen = true">
        <span class="lv-t-caption lv-tone-muted" x-text="nextStepHint() || @js(__('experiences.detail.booking.total'))">{{ __('booking_flow.hint.date') }}</span>
        <span class="lv-t-h5 lv-serif lv-tone-ink" x-text="canRequest() ? money(total()) : money(unitPrice())">{{ $page->booking['priceFrom'] }}</span>
    </button>
    <button type="button" class="lv-button lv-button--primary lv-t-label-l shrink-0" @click="canRequest() ? proceed() : (sheetOpen = true)">
        <span x-text="canRequest() ? @js(__('experiences.detail.booking.request')) : @js(__('experiences.detail.booking.check_dates'))">{{ __('experiences.detail.booking.check_dates') }}</span>
    </button>
</div>
