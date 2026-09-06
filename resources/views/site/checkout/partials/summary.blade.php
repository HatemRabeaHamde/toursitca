@php
    $experience = $checkoutSession->experience;
    $availability = $checkoutSession->availability;
    $title = $experience->getTranslation('title', app()->getLocale(), false);
    $thumbnail = $experience->media->firstWhere('type', 'image')?->publicUrl() ?? $experience->thumbnail;
    $thumbnailUrl = $thumbnail
        ? (\Illuminate\Support\Str::startsWith($thumbnail, ['http://', 'https://']) ? $thumbnail : asset($thumbnail))
        : config('landing.images.card_fallback');
    $dateLabel = $availability?->date?->translatedFormat('l, F j, Y');
    $timeLabel = $availability ? substr((string) $availability->time_slot, 0, 5) : null;
    $total = number_format((float) ($checkoutSession->price_snapshot['total'] ?? 0), 2);
@endphp

<aside class="booking-summary" aria-label="{{ __('ui.pages.order_summary_title') }}">
    <h2>{{ __('ui.pages.order_summary_title') }}</h2>

    <div class="booking-summary-card">
        <div class="booking-summary-media">
            <img src="{{ $thumbnailUrl }}" alt="{{ $title }}" loading="lazy" onerror="this.parentElement.classList.add('is-empty'); this.remove()">
        </div>

        <div class="booking-summary-head">
            <div>
                <h3>{{ $title }}</h3>
                @if ((float) $experience->rating_avg > 0)
                    <div class="booking-summary-rating">
                        {{ number_format((float) $experience->rating_avg, 1) }} / 5
                    </div>
                @endif
            </div>
        </div>

        <div class="booking-summary-section">
            <div class="booking-summary-list">
                <div class="booking-summary-item">
                    <span aria-hidden="true">•</span>
                    <span>
                        {{ __('booking.types.'.$checkoutSession->booking_type) }}
                        @if ($checkoutSession->tour_language)
                            <span class="booking-summary-muted">{{ __('ui.fields.languages') }}: {{ strtoupper($checkoutSession->tour_language) }}</span>
                        @endif
                    </span>
                </div>
                <div class="booking-summary-item">
                    <span aria-hidden="true">•</span>
                    <span>{{ $dateLabel }}@if ($timeLabel) · {{ $timeLabel }}@endif</span>
                </div>
                <div class="booking-summary-item">
                    <span aria-hidden="true">•</span>
                    <span>{{ trans_choice('ui.checkout.participants_summary', $checkoutSession->participants_count, ['count' => $checkoutSession->participants_count]) }}</span>
                </div>
            </div>
        </div>

        <div class="booking-summary-section">
            <div class="booking-summary-list">
                <div class="booking-summary-item">
                    <span aria-hidden="true">✓</span>
                    <span>{{ __('ui.checkout.free_cancellation') }}<span class="booking-summary-muted">{{ __('ui.checkout.free_cancellation_help') }}</span></span>
                </div>
                <div class="booking-summary-item">
                    <span aria-hidden="true">✓</span>
                    <span>{{ __('ui.checkout.pay_later') }}<span class="booking-summary-muted">{{ __('ui.checkout.pay_later_help') }}</span></span>
                </div>
            </div>
        </div>

        <div class="booking-summary-total">
            <span>{{ __('ui.fields.total_price') }}</span>
            <div>
                <strong>{{ $checkoutSession->currency }} {{ $total }}</strong>
                <small class="booking-summary-tax">{{ __('ui.checkout.taxes_included') }}</small>
            </div>
        </div>
    </div>
</aside>
