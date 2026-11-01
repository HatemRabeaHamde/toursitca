{{-- Phone / tablet bar: shows the next step (dates, option, request) and the running total. Lives inside the booking scope. --}}
<div class="lv-xbar">
    <p class="flex flex-col">
        <span class="lv-t-caption lv-tone-muted" x-text="store.selectedDate && !needsOption() ? @js(__('experiences.detail.booking.total')) : @js(__('experiences.detail.booking.from'))">{{ __('experiences.detail.booking.from') }}</span>
        <span class="lv-t-h5 lv-serif lv-tone-ink" x-text="store.selectedDate && !needsOption() ? money(total()) : money(unitPrice())">{{ $page->booking['priceFrom'] }}</span>
    </p>
    <button type="button" class="lv-button lv-button--primary lv-t-label-l" @click="store.selectedDate && !needsOption() ? request() : (store.selectedDate ? request() : openDates())">
        <span x-text="requestLabel() || @js(__('experiences.detail.booking.request'))">{{ __('experiences.detail.booking.check_dates') }}</span>
    </button>
</div>
