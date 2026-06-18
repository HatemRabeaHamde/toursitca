@extends('layouts.site')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.card>
            <x-ui.badge color="amber">{{ __('booking.status.'.$booking->status) }}</x-ui.badge>
            <h1 class="mt-4 text-2xl font-semibold text-slate-950">{{ __('ui.pages.booking_confirmation_title') }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('ui.messages.booking_pending_review') }}</p>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.experience') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $booking->experience->getTranslation('title', app()->getLocale(), false) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.agency_name') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $booking->experience->agency->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.availability') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $booking->availability->date->format('Y-m-d') }} · {{ substr($booking->availability->time_slot, 0, 5) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.booking_type') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ __('booking.types.'.$booking->booking_type) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.participants_count') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $booking->participants_count }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-slate-500">{{ __('ui.fields.total_price') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ number_format((float) $booking->total_price, 2) }} MAD</dd>
                </div>
            </dl>

            <div class="mt-6">
                <x-ui.button href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}">
                    {{ __('ui.nav.back_to_experiences') }}
                </x-ui.button>
            </div>
        </x-ui.card>
    </section>
@endsection
