@extends('layouts.site')

@section('hide_site_chrome', 'true')
@section('hide_site_flash', 'true')

@push('styles')
    @vite(['resources/css/booking-flow.css'])
@endpush

@section('content')
    @php
        $title = $experience->getTranslation('title', app()->getLocale(), false);
        $thumbnail = $experience->thumbnail;
        $thumbnailUrl = $thumbnail
            ? (\Illuminate\Support\Str::startsWith($thumbnail, ['http://', 'https://']) ? $thumbnail : asset($thumbnail))
            : config('landing.images.card_fallback');
    @endphp

    <div class="booking-flow">
        <div class="booking-shell">
            <div class="booking-topbar">
                <a href="{{ route('site.home', ['locale' => app()->getLocale()]) }}" class="booking-logo" aria-label="{{ __('ui.nav.home') }}">
                    Toursti<em>Ca</em>
                </a>
                <a href="{{ route('site.experiences.show', ['locale' => app()->getLocale(), 'experience' => $experience]) }}" class="booking-back">
                    {{ __('ui.experience_detail.back_to_experiences') }}
                </a>
            </div>

            <div class="booking-grid">
                <main class="booking-main">
                    <div class="booking-trip-row">
                        <div class="booking-trip-thumb">
                            <img src="{{ $thumbnailUrl }}" alt="{{ $title }}" loading="lazy">
                        </div>
                        <div>
                            <h1>{{ $title }}</h1>
                            <p>{{ $experience->agency->name }} · {{ $experience->location_city }}</p>
                        </div>
                    </div>

                    <h2 class="booking-title">{{ __('ui.pages.booking_title') }}</h2>
                    <p class="booking-copy">{{ $experience->getTranslation('description', app()->getLocale(), false) }}</p>

                    <div class="booking-card booking-card-pad">
                        @error('booking')
                            <p class="booking-error">{{ $message }}</p>
                        @enderror

                        @if ($availabilities->isEmpty())
                            <p class="booking-copy">{{ __('ui.messages.no_booking_slots') }}</p>
                        @else
                            <form method="POST" action="{{ route('site.bookings.store', ['locale' => app()->getLocale(), 'experience' => $experience]) }}">
                                @csrf

                                <div class="booking-field">
                                    <label for="availability_id" class="booking-label">{{ __('ui.fields.availability') }}</label>
                                    <select id="availability_id" name="availability_id" class="booking-input" required>
                                        <option value="">{{ __('ui.fields.select_slot') }}</option>
                                        @foreach ($availabilities as $availability)
                                            <option value="{{ $availability->id }}" @selected(old('availability_id') == $availability->id)>
                                                {{ $availability->date->translatedFormat('Y-m-d') }} · {{ substr((string) $availability->time_slot, 0, 5) }} · {{ __('ui.fields.available_seats') }}: {{ $availability->availableSeats() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('availability_id')
                                        <p class="booking-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="booking-form-grid">
                                    <div class="booking-field">
                                        <label for="booking_type" class="booking-label">{{ __('ui.fields.booking_type') }}</label>
                                        <select id="booking_type" name="booking_type" class="booking-input" required>
                                            <option value="group" @selected(old('booking_type', 'group') === 'group')>{{ __('booking.types.group') }}</option>
                                            @if ($experience->supportsPrivateBooking())
                                                <option value="private" @selected(old('booking_type') === 'private')>{{ __('booking.types.private') }}</option>
                                            @endif
                                        </select>
                                        @error('booking_type')
                                            <p class="booking-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="booking-field">
                                        <label for="participants_count" class="booking-label">{{ __('ui.fields.participants_count') }}</label>
                                        <input id="participants_count" name="participants_count" type="number" min="1" max="{{ $experience->max_group_size }}" class="booking-input" value="{{ old('participants_count', 1) }}" required>
                                        @error('participants_count')
                                            <p class="booking-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="booking-form-grid">
                                    <div class="booking-field">
                                        <label for="guest_name" class="booking-label">{{ __('ui.fields.guest_name') }}</label>
                                        <input id="guest_name" name="guest_name" class="booking-input" value="{{ old('guest_name', auth()->user()?->name) }}" required autocomplete="name">
                                        @error('guest_name')
                                            <p class="booking-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="booking-field">
                                        <label for="guest_email" class="booking-label">{{ __('ui.fields.guest_email') }}</label>
                                        <input id="guest_email" name="guest_email" type="email" class="booking-input" value="{{ old('guest_email', auth()->user()?->email) }}" required autocomplete="email">
                                        @error('guest_email')
                                            <p class="booking-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="booking-field">
                                    <label for="guest_phone" class="booking-label">{{ __('ui.fields.guest_phone') }}</label>
                                    <input id="guest_phone" name="guest_phone" class="booking-input" value="{{ old('guest_phone') }}" autocomplete="tel">
                                    @error('guest_phone')
                                        <p class="booking-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="booking-field">
                                    <label for="special_notes" class="booking-label">{{ __('ui.fields.special_notes') }}</label>
                                    <textarea id="special_notes" name="special_notes" class="booking-textarea">{{ old('special_notes') }}</textarea>
                                    @error('special_notes')
                                        <p class="booking-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="booking-actions">
                                    <button type="submit" class="booking-primary">
                                        {{ __('ui.nav.confirm_booking') }}
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </main>

                <aside class="booking-summary">
                    <h2>{{ __('ui.pages.order_summary_title') }}</h2>
                    <div class="booking-summary-card">
                        <div class="booking-summary-head">
                            <img src="{{ $thumbnailUrl }}" alt="{{ $title }}" loading="lazy">
                            <div>
                                <h3>{{ $title }}</h3>
                                <div class="booking-summary-rating">
                                    {{ number_format((float) $experience->rating_avg, 1) }} / 5
                                </div>
                            </div>
                        </div>
                        <div class="booking-summary-section">
                            <div class="booking-summary-list">
                                <div class="booking-summary-item">
                                    <span aria-hidden="true">•</span>
                                    <span>{{ trans_choice('ui.units.hours', (int) ceil((float) $experience->duration_hours), ['count' => rtrim(rtrim(number_format((float) $experience->duration_hours, 1), '0'), '.')]) }}</span>
                                </div>
                                <div class="booking-summary-item">
                                    <span aria-hidden="true">•</span>
                                    <span>{{ __('ui.fields.max_group_size') }}: {{ $experience->max_group_size }}</span>
                                </div>
                                <div class="booking-summary-item">
                                    <span aria-hidden="true">•</span>
                                    <span>{{ __('ui.fields.price_per_person') }}: MAD {{ number_format((float) $experience->price_per_person, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection
