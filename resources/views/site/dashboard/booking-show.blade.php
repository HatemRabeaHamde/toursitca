@extends('layouts.tourist')

@section('content')
    @php
        $locale = app()->getLocale();
        $steps = ['pending', 'confirmed', 'completed'];
        $currentIndex = array_search($booking->status, $steps, true);
        $isCancelled = $booking->status === 'cancelled';
    @endphp

    <a href="{{ route('site.dashboard.bookings', ['locale' => $locale]) }}" class="td-back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        {{ __('ui.pages.my_trips_title') }}
    </a>

    <div class="td-detail-head">
        <div>
            <p class="td-eyebrow">#{{ $booking->id }} · {{ $booking->experience->agency->name }}</p>
            <h1 class="td-h1">{{ $booking->experience->getTranslation('title', $locale, false) }}</h1>
        </div>
        <span class="td-badge is-{{ $booking->status }}" style="font-size:12px; height:26px; padding:0 14px;">
            {{ __('booking.status.'.$booking->status) }}
        </span>
    </div>

    {{-- Status timeline --}}
    <div class="td-timeline">
        @foreach ($steps as $i => $step)
            <div class="td-timeline-step {{ $isCancelled ? 'is-cancelled' : ($currentIndex !== false && $i <= $currentIndex ? 'is-done' : '') }}">
                <span class="td-timeline-dot">
                    @if ($isCancelled)
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    @elseif ($currentIndex !== false && $i <= $currentIndex)
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </span>
                <span class="td-timeline-label">{{ __('booking.status.'.$step) }}</span>
            </div>
        @endforeach
    </div>

    <div class="td-detail-grid">
        {{-- Left column — trip + contact info --}}
        <div>
            <div class="td-panel">
                <p class="td-panel-title">{{ __('ui.dashboard.trip_details') }}</p>

                <div style="margin-top:16px;">
                    <div class="td-info-row">
                        <span class="td-info-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </span>
                        <div>
                            <p class="td-info-label">{{ __('ui.fields.date') }}</p>
                            <p class="td-info-value">
                                {{ $booking->availability->date->translatedFormat('l, F j, Y') }}
                                · {{ substr((string) $booking->availability->time_slot, 0, 5) }}
                            </p>
                        </div>
                    </div>

                    <div class="td-info-row">
                        <span class="td-info-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <div>
                            <p class="td-info-label">{{ __('ui.fields.booking_type') }}</p>
                            <p class="td-info-value">
                                {{ __('booking.types.'.$booking->booking_type) }}
                                · {{ trans_choice('ui.checkout.participants_summary', $booking->participants_count, ['count' => $booking->participants_count]) }}
                            </p>
                        </div>
                    </div>

                    @if ($booking->tour_language)
                        <div class="td-info-row">
                            <span class="td-info-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            </span>
                            <div>
                                <p class="td-info-label">{{ __('ui.fields.languages') }}</p>
                                <p class="td-info-value">{{ strtoupper($booking->tour_language) }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($booking->experience->meeting_point)
                        <div class="td-info-row">
                            <span class="td-info-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </span>
                            <div>
                                <p class="td-info-label">{{ __('ui.fields.meeting_point') }}</p>
                                <p class="td-info-value">{{ $booking->experience->meeting_point }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($booking->pickup_address)
                        <div class="td-info-row">
                            <span class="td-info-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 19l-6 3V6l6-3 8 3 6-3v16l-6 3-8-3z"/></svg>
                            </span>
                            <div>
                                <p class="td-info-label">{{ __('ui.fields.pickup_address') }}</p>
                                <p class="td-info-value">{{ $booking->pickup_address }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($booking->special_notes)
                        <div class="td-info-row">
                            <span class="td-info-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            </span>
                            <div>
                                <p class="td-info-label">{{ __('ui.fields.special_notes') }}</p>
                                <p class="td-info-value">{{ $booking->special_notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="td-panel">
                <p class="td-panel-title">{{ __('ui.experience_detail.agency_title') }}</p>

                <div style="margin-top:16px;">
                    <div class="td-info-row" style="border-top:0; padding-top:0;">
                        <span class="td-info-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        </span>
                        <div>
                            <p class="td-info-label">{{ __('ui.fields.agency_name') }}</p>
                            <p class="td-info-value">{{ $booking->experience->agency->name }}</p>
                        </div>
                    </div>

                    @if ($booking->experience->agency->phone)
                        <div class="td-info-row">
                            <span class="td-info-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </span>
                            <div>
                                <p class="td-info-label">{{ __('ui.fields.phone') }}</p>
                                <p class="td-info-value">{{ $booking->experience->agency->phone }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right column — price summary + actions --}}
        <div>
            <div class="td-panel">
                <p class="td-panel-title">{{ __('ui.pages.order_summary_title') }}</p>

                <div class="td-price-summary" style="margin-top:16px;">
                    <div class="td-price-row">
                        <span>{{ __('booking.types.'.$booking->booking_type) }} · {{ trans_choice('ui.checkout.participants_summary', $booking->participants_count, ['count' => $booking->participants_count]) }}</span>
                        <span>{{ $booking->unit_price }} MAD</span>
                    </div>
                    <div class="td-price-row is-total">
                        <span>{{ __('ui.fields.total_price') }}</span>
                        <strong>{{ $booking->total_price }} MAD</strong>
                    </div>
                </div>
            </div>

            @if ($booking->status === 'completed')
                <div class="td-panel">
                    @if ($booking->review)
                        <p class="td-panel-title">{{ __('ui.dashboard.review_submitted') }}</p>
                        <div class="td-stars" style="margin-top:10px;" aria-label="{{ $booking->review->rating }} / 5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="{{ $i <= $booking->review->rating ? 'var(--gold)' : 'none' }}" stroke="var(--gold)" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @endfor
                        </div>
                        <p class="td-review-body" style="margin-top:10px;">{{ $booking->review->body }}</p>
                    @else
                        <p class="td-panel-title">{{ __('ui.dashboard.write_review') }}</p>
                        <p class="td-panel-sub">{{ __('ui.dashboard.write_review_intro') }}</p>
                        <a href="{{ route('site.reviews.create', ['locale' => $locale, 'booking' => $booking->id]) }}" class="td-btn" style="margin-top:16px; width:100%;">
                            {{ __('ui.dashboard.write_review') }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
