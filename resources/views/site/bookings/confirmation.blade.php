@extends('layouts.site')

@section('hide_site_chrome', 'true')
@section('hide_site_flash', 'true')

@push('styles')
    @vite(['resources/css/booking-flow.css'])
@endpush

@section('content')
    <div class="booking-flow">
        <div class="booking-shell">
            <div class="booking-topbar">
                <a href="{{ route('site.home', ['locale' => app()->getLocale()]) }}" class="booking-logo" aria-label="{{ __('ui.nav.home') }}">
                    Toursti<em>Ca</em>
                </a>
                <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="booking-back">
                    {{ __('ui.nav.back_to_experiences') }}
                </a>
            </div>

            <section class="booking-confirm">
                <div class="booking-card booking-confirm-card">
                    <div class="booking-confirm-mark" aria-hidden="true">✓</div>
                    <h1>{{ __('ui.pages.booking_confirmation_title') }}</h1>
                    <p>{{ __('ui.messages.booking_pending_review') }}</p>

                    <dl class="booking-confirm-grid">
                        <div>
                            <dt>{{ __('ui.fields.experience') }}</dt>
                            <dd>{{ $booking->experience->getTranslation('title', app()->getLocale(), false) }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('ui.fields.agency_name') }}</dt>
                            <dd>{{ $booking->experience->agency->name }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('ui.fields.availability') }}</dt>
                            <dd>{{ $booking->availability->date->translatedFormat('Y-m-d') }} · {{ substr((string) $booking->availability->time_slot, 0, 5) }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('ui.fields.booking_type') }}</dt>
                            <dd>{{ __('booking.types.'.$booking->booking_type) }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('ui.fields.participants_count') }}</dt>
                            <dd>{{ $booking->participants_count }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('ui.fields.total_price') }}</dt>
                            <dd>{{ number_format((float) $booking->total_price, 2) }} {{ config('payment.currency', 'MAD') }}</dd>
                        </div>
                    </dl>

                    <div class="booking-actions">
                        <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="booking-primary">
                            {{ __('ui.nav.back_to_experiences') }}
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
