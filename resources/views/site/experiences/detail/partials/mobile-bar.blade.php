{{-- Phone / tablet: price + jump to the booking card. --}}
<div class="lv-xbar">
    <p class="flex flex-col">
        <span class="lv-t-caption lv-tone-muted">{{ __('experiences.detail.booking.from') }}</span>
        <span class="lv-t-h5 lv-serif lv-tone-ink">{{ $page->booking['priceFrom'] }} <span class="lv-t-caption lv-tone-muted">{{ __('experiences.detail.booking.per_person') }}</span></span>
    </p>
    <x-site.atoms.button href="#book" :arrow="false">{{ __('experiences.detail.booking.check_dates') }}</x-site.atoms.button>
</div>
