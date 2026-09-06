@extends('layouts.site')

@section('hide_site_chrome', 'true')
@section('hide_site_flash', 'true')

@push('styles')
    @vite(['resources/css/booking-flow.css'])
@endpush

@section('content')
    <div class="booking-flow">
        <div class="booking-shell">
            @include('site.checkout.partials.steps', ['currentStep' => 'payment'])

            <div class="booking-grid">
                <main class="booking-main">
                    <h1 class="booking-title">{{ __('ui.pages.checkout_payment_title') }}</h1>
                    <p class="booking-copy">{{ __('ui.checkout.payment_intro') }}</p>

                    <div class="booking-card booking-card-pad">
                        <h2 class="booking-section-title">{{ __('ui.checkout.manual_payment_title') }}</h2>
                        <p class="booking-copy">{{ __('ui.checkout.manual_payment_body') }}</p>

                        <dl class="booking-confirm-grid">
                            <div>
                                <dt>{{ __('ui.fields.payment_method') }}</dt>
                                <dd>{{ __('ui.payment_methods.'.$checkoutSession->payment_method) }}</dd>
                            </div>
                            <div>
                                <dt>{{ __('ui.fields.payment_status') }}</dt>
                                <dd>{{ __('ui.payment_status.'.$checkoutSession->payment_status) }}</dd>
                            </div>
                        </dl>

                        <form method="POST" action="{{ route('site.checkout.payment.confirm', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" class="booking-actions" data-checkout-form>
                            @csrf
                            <button type="submit" class="booking-primary">
                                {{ __('ui.nav.confirm_booking') }}
                            </button>
                        </form>
                    </div>
                </main>

                @include('site.checkout.partials.summary', ['checkoutSession' => $checkoutSession])
            </div>
        </div>
    </div>
@endsection
