@extends('layouts.site')

@section('content')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1fr)_24rem] lg:px-8">
        <div>
            <p class="text-sm font-medium text-slate-600">{{ $experience->agency->name }} · {{ $experience->location_city }}</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-950">{{ $experience->getTranslation('title', app()->getLocale(), false) }}</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $experience->getTranslation('description', app()->getLocale(), false) }}</p>

            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                <x-ui.card>
                    <p class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.duration_hours') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-950">{{ $experience->duration_hours }}</p>
                </x-ui.card>
                <x-ui.card>
                    <p class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.max_group_size') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-950">{{ $experience->max_group_size }}</p>
                </x-ui.card>
                <x-ui.card>
                    <p class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.price_per_person') }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-950">{{ number_format((float) $experience->price_per_person, 2) }} MAD</p>
                </x-ui.card>
            </div>
        </div>

        <x-ui.card>
            <h2 class="text-base font-semibold text-slate-950">{{ __('ui.pages.booking_title') }}</h2>

            @error('booking')
                <x-ui.alert type="error" class="mt-4">{{ $message }}</x-ui.alert>
            @enderror

            @if ($availabilities->isEmpty())
                <div class="mt-4">
                    <x-ui.empty-state :title="__('ui.messages.no_booking_slots')" />
                </div>
            @else
                <form method="POST" action="{{ route('site.bookings.store', ['locale' => app()->getLocale(), 'experience' => $experience]) }}" class="mt-5 space-y-4">
                    @csrf

                    <label class="block">
                        <span class="label">{{ __('ui.fields.availability') }}</span>
                        <select name="availability_id" class="input" required>
                            <option value="">{{ __('ui.fields.select_slot') }}</option>
                            @foreach ($availabilities as $availability)
                                <option value="{{ $availability->id }}" @selected(old('availability_id') == $availability->id)>
                                    {{ $availability->date->format('Y-m-d') }} · {{ substr($availability->time_slot, 0, 5) }} · {{ __('ui.fields.available_seats') }}: {{ $availability->availableSeats() }}
                                </option>
                            @endforeach
                        </select>
                        @error('availability_id')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block">
                        <span class="label">{{ __('ui.fields.booking_type') }}</span>
                        <select name="booking_type" class="input" required>
                            <option value="group" @selected(old('booking_type', 'group') === 'group')>{{ __('booking.types.group') }}</option>
                            @if ($experience->supportsPrivateBooking())
                                <option value="private" @selected(old('booking_type') === 'private')>{{ __('booking.types.private') }}</option>
                            @endif
                        </select>
                        @error('booking_type')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </label>

                    <x-ui.input name="participants_count" type="number" min="1" max="{{ $experience->max_group_size }}" :label="__('ui.fields.participants_count')" :value="old('participants_count', 1)" required />
                    <x-ui.input name="guest_name" :label="__('ui.fields.guest_name')" :value="old('guest_name', auth()->user()?->name)" required />
                    <x-ui.input name="guest_email" type="email" :label="__('ui.fields.guest_email')" :value="old('guest_email', auth()->user()?->email)" required />
                    <x-ui.input name="guest_phone" :label="__('ui.fields.guest_phone')" :value="old('guest_phone')" />
                    <x-ui.textarea name="special_notes" :label="__('ui.fields.special_notes')" :value="old('special_notes')" />

                    <x-ui.button type="submit" class="w-full justify-center">
                        {{ __('ui.nav.confirm_booking') }}
                    </x-ui.button>
                </form>
            @endif
        </x-ui.card>
    </section>
@endsection
