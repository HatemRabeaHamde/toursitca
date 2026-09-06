@php
    $ctaImage = $detail->gallery[0]['url'] ?? null;
@endphp

<section class="xp-cta">
    <div class="xp-cta-card">
        @if ($ctaImage)
            <img class="xp-cta-bg" src="{{ $ctaImage }}" alt="" aria-hidden="true" loading="lazy">
        @endif
        <div class="xp-cta-veil" aria-hidden="true"></div>

        <div class="xp-cta-content">
            <p class="xp-cta-kicker">{{ $experience->location_city }} · {{ __('ui.categories.' . $experience->category) }}</p>
            <h2 class="xp-cta-title">{{ __('ui.experience_detail.cta.title') }}</h2>
            <p class="xp-cta-sub">{{ __('ui.experience_detail.cta.subtitle') }}</p>

            <div class="xp-cta-row">
                <a href="#booking-options" class="xp-cta-btn">
                    {{ __('ui.experience_detail.cta.button') }}
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <div class="xp-cta-price">
                    <span>{{ __('ui.labels.from') }}</span>
                    <strong>{{ $detail->bookingSummary['currency'] }} {{ $detail->bookingSummary['price_from'] }}</strong>
                    <em>/ {{ __('ui.price_type.per_person') }}</em>
                </div>
            </div>

            <div class="xp-cta-trust">
                <span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ __('ui.experience_detail.booking_panel.free_cancellation_title') }}
                </span>
                <span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ __('ui.experience_detail.key_specs.pay_later_title') }}
                </span>
            </div>
        </div>
    </div>
</section>
