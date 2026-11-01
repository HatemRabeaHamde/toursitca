{{-- Pick-up point (step 1 of the request): pin the place on a map, or leave it for the host. Data: CheckoutSession $checkoutSession, trip array $trip. --}}
@extends('layouts.site-v2')

@section('nav_active', 'experiences')

@push('page-styles')
    @vite(['resources/css/pages/account.css'])
@endpush

@php
    $pickupStatus = old('pickup_status', 'add_now');
    $experience = $checkoutSession->experience;
@endphp

@section('page')
    <div id="content" class="lv-flow relative overflow-x-clip">
        <div class="lv-container lv-flow__inner">
            <a href="{{ $trip['href'] }}#book" class="lv-flow__back lv-t-ui-s lv-tone-muted"><x-site.atoms.icon name="arrow-right-ink" :size="16" class="lv-flow__back-icon" />{{ __('booking_flow.pickup.back') }}</a>
            <x-site.atoms.eyebrow>{{ __('booking_flow.pickup.eyebrow') }}</x-site.atoms.eyebrow>
            <h1 class="lv-t-h1 lv-tone-ink lv-flow__title">{{ __('booking_flow.pickup.title') }} <em class="lv-italic">{{ __('booking_flow.pickup.accent') }}</em></h1>
            <p class="lv-t-body-l lv-tone-muted lv-flow__lede">{{ __('booking_flow.pickup.lede') }}</p>

            @include('site.checkout.sections.steps', ['current' => 1])

            @foreach(['checkout_session', 'availability_id'] as $problem)
                @error($problem)<p class="lv-flow__alert lv-t-body-s" role="alert">{{ $message }}</p>@enderror
            @endforeach

            <div class="lv-flow__grid">
                <form method="POST" action="{{ route('site.checkout.activity.store', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}"
                      class="lv-flow__main" x-data="{ pickupStatus: @js($pickupStatus) }">
                    @csrf

                    <fieldset class="lv-flow-card">
                        <legend class="sr-only">{{ __('booking_flow.pickup.pin_title') }}</legend>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach(['add_now' => ['pin_title', 'pin_help'], 'unknown' => ['later_title', 'later_help']] as $value => [$title, $help])
                                <label class="lv-v1-choice" :class="{ 'is-selected': pickupStatus === @js($value) }">
                                    <input type="radio" name="pickup_status" value="{{ $value }}" class="sr-only" x-model="pickupStatus">
                                    <x-site.atoms.icon :name="$value === 'add_now' ? 'pin-terracotta-18' : 'clock'" :size="20" />
                                    <span class="flex min-w-0 flex-col">
                                        <span class="lv-t-label-m lv-tone-ink">{{ __('booking_flow.pickup.'.$title) }}</span>
                                        <span class="lv-t-micro lv-tone-muted">{{ __('booking_flow.pickup.'.$help) }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div class="lv-pickmap" x-show="pickupStatus === 'add_now'" x-cloak
                             x-data="checkoutPickupMap({
                                 lat: @js(old('pickup_lat', $checkoutSession->pickup_lat)),
                                 lng: @js(old('pickup_lng', $checkoutSession->pickup_lng)),
                                 defaultLat: @js($experience->location_lat),
                                 defaultLng: @js($experience->location_lng),
                                 failed: @js(__('booking_flow.pickup.locate_failed')),
                             })"
                             x-effect="if (pickupStatus === 'add_now') loadMap()">
                            <div class="lv-pickmap__stage">
                                <div x-ref="pickupMap" class="lv-pickmap__canvas" role="application" aria-label="{{ __('booking_flow.pickup.map_label') }}"></div>
                                <p class="lv-pickmap__hint lv-t-label-s" x-show="mapReady && !hasPoint" x-transition.opacity>
                                    <x-site.atoms.icon name="pin" :size="16" />{{ __('booking_flow.pickup.hint') }}
                                </p>
                            </div>

                            <div class="lv-pickmap__bar">
                                <button type="button" class="lv-pickmap__btn lv-t-label-s" @click="locate()" :disabled="locating">
                                    <x-site.atoms.icon name="pin-terracotta" :size="16" />
                                    <span x-text="locating ? @js(__('booking_flow.pickup.locating')) : @js(__('booking_flow.pickup.locate'))">{{ __('booking_flow.pickup.locate') }}</span>
                                </button>
                                <button type="button" class="lv-pickmap__btn lv-pickmap__btn--quiet lv-t-label-s" x-show="hasPoint" x-cloak @click="clearPoint()">{{ __('booking_flow.pickup.clear') }}</button>
                                <p class="lv-t-caption lv-tone-muted lv-pickmap__coords">
                                    {{ __('booking_flow.pickup.coords') }}:
                                    <strong class="lv-tone-ink" x-text="hasPoint ? `${lat}, ${lng}` : @js(__('booking_flow.pickup.coords_none'))"></strong>
                                </p>
                            </div>
                            <p class="lv-form-error lv-t-caption" x-show="locateError" x-text="locateError" role="alert" x-cloak></p>

                            <input type="hidden" name="pickup_lat" :value="lat">
                            <input type="hidden" name="pickup_lng" :value="lng">

                            <div class="lv-form-field">
                                <label for="pickup_address" class="lv-t-label-s lv-tone-ink">{{ __('booking_flow.pickup.address') }} <span class="lv-tone-muted">· {{ __('booking_flow.pickup.address_optional') }}</span></label>
                                <input id="pickup_address" name="pickup_address" class="lv-form-input" maxlength="500" autocomplete="street-address"
                                       value="{{ old('pickup_address', $checkoutSession->pickup_address) }}" placeholder="{{ __('booking_flow.pickup.address_placeholder') }}">
                                @error('pickup_address')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
                            </div>
                            @error('pickup_lat')<p class="lv-form-error lv-t-caption" role="alert">{{ __('booking_flow.pickup.pin_needed') }}</p>@enderror
                        </div>

                        @error('pickup_status')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
                    </fieldset>

                    <div class="flex flex-wrap items-center gap-4">
                        <x-site.atoms.button type="submit">{{ __('booking_flow.pickup.continue') }}</x-site.atoms.button>
                        <p class="lv-t-caption lv-tone-muted">{{ __('booking_flow.pickup.note') }}</p>
                    </div>
                </form>

                <aside class="lv-flow__aside" aria-label="{{ __('account.checkout.summary') }}">
                    @include('site.checkout.sections.summary')
                </aside>
            </div>
        </div>
    </div>
@endsection
