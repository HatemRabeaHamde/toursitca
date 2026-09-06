@extends('layouts.tourist')

@section('content')
    @php($locale = app()->getLocale())

    <div class="td-page-head">
        <div>
            <p class="td-eyebrow">{{ __('ui.pages.tourist_title') }}</p>
            <h1 class="td-h1">{{ __('ui.pages.my_trips_title') }}</h1>
            <p class="td-page-intro">{{ __('ui.dashboard.my_trips_intro') }}</p>
        </div>

        <a href="{{ route('site.experiences.index', ['locale' => $locale]) }}" class="td-cta">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            {{ __('ui.nav.experiences') }}
        </a>
    </div>

    @if ($bookings->isEmpty())
        <div class="td-empty">
            <span class="td-empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9m9 5h-9m9 5h-9M5 7h.01M5 12h.01M5 17h.01"/></svg>
            </span>
            <h2>{{ __('ui.messages.no_bookings') }}</h2>
            <p>{{ __('ui.dashboard.no_trips_intro') }}</p>
            <a href="{{ route('site.experiences.index', ['locale' => $locale]) }}" class="td-cta">{{ __('ui.nav.experiences') }}</a>
        </div>
    @else
        <div class="td-list">
            @foreach ($bookings as $booking)
                <a href="{{ route('site.dashboard.bookings.show', ['locale' => $locale, 'booking' => $booking->id]) }}" class="td-card" style="text-decoration:none; color:inherit;">
                    <div class="td-card-body">
                        <div>
                            <div class="td-card-title-row">
                                <span class="td-card-title">{{ $booking->experience->getTranslation('title', $locale, false) }}</span>
                                <span class="td-badge is-{{ $booking->status }}">{{ __('booking.status.'.$booking->status) }}</span>
                            </div>

                            <p class="td-card-meta">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ $booking->availability->date->translatedFormat('l, F j, Y') }}
                                · {{ substr((string) $booking->availability->time_slot, 0, 5) }}
                                · {{ trans_choice('ui.checkout.participants_summary', $booking->participants_count, ['count' => $booking->participants_count]) }}
                            </p>

                            <p class="td-card-sub">{{ $booking->experience->agency->name }} · #{{ $booking->id }}</p>
                        </div>

                        <div class="td-card-side">
                            <div style="text-align:right;">
                                <p class="td-card-price-label">{{ __('ui.fields.total_price') }}</p>
                                <p class="td-card-price">{{ $booking->total_price }} MAD</p>
                            </div>

                            @if ($booking->status === 'completed')
                                @if ($booking->review)
                                    <span class="td-review-done">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        {{ __('ui.dashboard.review_submitted') }}
                                    </span>
                                @else
                                    <span
                                        class="td-cta-outline"
                                        role="link"
                                        tabindex="0"
                                        onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('site.reviews.create', ['locale' => $locale, 'booking' => $booking->id]) }}'"
                                    >{{ __('ui.dashboard.write_review') }}</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($bookings->hasPages())
            <div class="mt-8">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif
@endsection
