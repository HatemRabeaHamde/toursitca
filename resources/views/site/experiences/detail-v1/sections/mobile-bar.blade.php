{{-- Phone / tablet bar: the next missing step (or the running total) and a request button that stays inactive until the trip is configured. Lives inside the booking scope. --}}
<div class="lv-xbar">
    <a :href="store.selectedDate ? '#options' : '#book'" class="flex min-w-0 flex-col">
        <span class="lv-t-caption lv-tone-muted" x-text="nextStepHint() || @js(__('experiences.detail.booking.total'))">{{ __('booking_flow.hint.date') }}</span>
        <span class="lv-t-h5 lv-serif lv-tone-ink" x-text="canRequest() ? money(total()) : money(unitPrice())">{{ $page->booking['priceFrom'] }}</span>
    </a>
    <button type="button" class="lv-button lv-button--primary lv-t-label-l shrink-0" disabled :disabled="!canRequest()" @click="proceed()">
        <span>{{ __('experiences.detail.booking.request') }}</span>
    </button>
</div>
