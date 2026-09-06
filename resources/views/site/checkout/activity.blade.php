@extends('layouts.site')

@section('hide_site_chrome', 'true')
@section('hide_site_flash', 'true')

@push('styles')
    @vite(['resources/css/booking-flow.css'])
@endpush

@section('content')
    @php
        $experience = $checkoutSession->experience;
        $availability = $checkoutSession->availability;
        $pickupStatus = old('pickup_status', $checkoutSession->pickup_status ?: 'unknown');
        $title = $experience->getTranslation('title', app()->getLocale(), false);
        $thumbnail = $experience->media->firstWhere('type', 'image')?->publicUrl() ?? $experience->thumbnail;
        $thumbnailUrl = $thumbnail
            ? (\Illuminate\Support\Str::startsWith($thumbnail, ['http://', 'https://']) ? $thumbnail : asset($thumbnail))
            : config('landing.images.card_fallback');
        $holdMinutes = max(0, (int) ceil(now()->diffInSeconds($checkoutSession->reserved_until, false) / 60));
    @endphp

    <div class="booking-flow">
        <div class="booking-shell">
            @include('site.checkout.partials.steps', ['currentStep' => 'activity'])

            <div class="booking-grid">
                <main class="booking-main">
                    <div class="booking-activity-panel">
                        <div class="booking-hold">
                            <span aria-hidden="true">◷</span>
                            {{ __('ui.messages.checkout_hold_notice', ['minutes' => $holdMinutes]) }}
                        </div>

                        <div class="booking-trip-row">
                            <div class="booking-trip-thumb">
                                <img src="{{ $thumbnailUrl }}" alt="{{ $title }}" loading="lazy" onerror="this.parentElement.classList.add('is-empty'); this.remove()">
                            </div>
                            <div>
                                <p class="booking-kicker">{{ __('ui.checkout.steps.activity') }}</p>
                                <h1>{{ $title }}</h1>
                                <p>{{ $availability->date->translatedFormat('l, F j, Y') }} · {{ substr((string) $availability->time_slot, 0, 5) }}</p>
                            </div>
                        </div>
                    </div>

                    <h2 class="booking-title">{{ __('ui.checkout.pickup_question') }}</h2>
                    <p class="booking-copy">{{ __('ui.checkout.pickup_question_help') }}</p>

                    <form method="POST"
                        action="{{ route('site.checkout.activity.store', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}"
                        data-checkout-form
                        x-data="{ pickupStatus: @js($pickupStatus) }">
                        @csrf

                        <div class="booking-card">
                            <label class="booking-choice">
                                <input type="radio" name="pickup_status" value="add_now" x-model="pickupStatus">
                                <span>
                                    <span class="booking-choice-title">{{ __('ui.checkout.pickup_add_now') }}</span>
                                    <span class="booking-choice-help">{{ __('ui.checkout.pickup_add_now_help') }}</span>
                                </span>
                            </label>

                            <div class="booking-pickup-panel"
                                x-data="checkoutPickupMap({
                                    lat: @js(old('pickup_lat', $checkoutSession->pickup_lat)),
                                    lng: @js(old('pickup_lng', $checkoutSession->pickup_lng)),
                                    defaultLat: @js($experience->location_lat),
                                    defaultLng: @js($experience->location_lng)
                                })"
                                x-effect="if (pickupStatus === 'add_now') loadMap()"
                                x-show="pickupStatus === 'add_now'"
                                x-transition.opacity
                                x-cloak>
                                <p class="booking-choice-help">{{ __('ui.checkout.pickup_map_help') }}</p>

                                <div class="booking-field">
                                    <label for="pickup_address" class="booking-label">{{ __('ui.fields.pickup_address') }} <span>{{ __('ui.fields.optional') }}</span></label>
                                    <input id="pickup_address" name="pickup_address" class="booking-input" value="{{ old('pickup_address', $checkoutSession->pickup_address) }}" placeholder="{{ __('ui.checkout.pickup_address_placeholder') }}" autocomplete="street-address">
                                    @error('pickup_address')
                                        <p class="booking-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <input type="hidden" name="pickup_lat" :value="lat">
                                <input type="hidden" name="pickup_lng" :value="lng">

                                <div class="booking-map-wrap">
                                    <div class="booking-map-head">
                                        <p>{{ __('ui.checkout.pickup_map_hint') }}</p>
                                        <button type="button" class="booking-map-clear" x-show="hasPoint" x-on:click="clearPoint()">
                                            {{ __('ui.checkout.clear_pickup') }}
                                        </button>
                                    </div>
                                    <div x-ref="pickupMap" class="booking-map-canvas" role="application" aria-label="{{ __('ui.fields.location_map') }}"></div>
                                    <div class="booking-map-meta">
                                        <span>{{ __('ui.fields.selected_coordinates') }}</span>
                                        <strong x-text="hasPoint ? `${lat}, ${lng}` : @js(__('ui.checkout.coordinates_pending'))"></strong>
                                    </div>
                                </div>

                                @error('pickup_lat')
                                    <p class="booking-error">{{ $message }}</p>
                                @enderror
                                @error('pickup_lng')
                                    <p class="booking-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <label class="booking-choice">
                                <input type="radio" name="pickup_status" value="unknown" x-model="pickupStatus">
                                <span>
                                    <span class="booking-choice-title">{{ __('ui.checkout.pickup_unknown') }}</span>
                                    <span class="booking-choice-help">{{ __('ui.checkout.pickup_unknown_help') }}</span>
                                </span>
                            </label>
                        </div>

                        @error('pickup_status')
                            <p class="booking-error">{{ $message }}</p>
                        @enderror

                        <div class="booking-actions">
                            <button type="submit" class="booking-primary">
                                {{ __('ui.nav.next_contact_details') }}
                            </button>
                        </div>
                    </form>
                </main>

                @include('site.checkout.partials.summary', ['checkoutSession' => $checkoutSession])
            </div>
        </div>
    </div>
@endsection
