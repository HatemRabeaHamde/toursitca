@extends('layouts.agency')

@section('title', __('ui.pages.bookings_title'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('agency.bookings.index') }}" class="flex flex-wrap items-end gap-3">
            <label class="block">
                <span class="label">{{ __('ui.fields.status') }}</span>
                <select name="status" class="input min-w-40">
                    <option value="">{{ __('ui.fields.all_statuses') }}</option>
                    @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $bookingStatus)
                        <option value="{{ $bookingStatus }}" @selected($status === $bookingStatus)>{{ __('booking.status.'.$bookingStatus) }}</option>
                    @endforeach
                </select>
            </label>
            <x-ui.button type="submit" variant="secondary">{{ __('ui.nav.filter') }}</x-ui.button>
        </form>
    </div>

    <x-ui.card :title="__('ui.pages.bookings_title')">
        @if ($bookings->isEmpty())
            <x-ui.empty-state :title="__('ui.messages.no_bookings')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="text-left text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="py-3 pr-4">{{ __('ui.fields.reference') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.guest_name') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.experience') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.availability') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.booking_type') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.total_price') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="py-3 pr-4 font-medium text-slate-950">#{{ $booking->id }}</td>
                                <td class="py-3 pr-4 text-slate-600">
                                    <div class="font-medium text-slate-950">{{ $booking->guest_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $booking->guest_email }}</div>
                                </td>
                                <td class="py-3 pr-4 text-slate-600">{{ $booking->experience->getTranslation('title', app()->getLocale(), false) }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ $booking->availability->date->format('Y-m-d') }} · {{ substr($booking->availability->time_slot, 0, 5) }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ __('booking.types.'.$booking->booking_type) }} · {{ $booking->participants_count }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ number_format((float) $booking->total_price, 2) }} MAD</td>
                                <td class="py-3 pr-4">
                                    <x-ui.badge :color="$booking->status === 'confirmed' ? 'green' : ($booking->status === 'cancelled' ? 'red' : 'amber')">
                                        {{ __('booking.status.'.$booking->status) }}
                                    </x-ui.badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$bookings" />
        @endif
    </x-ui.card>
@endsection
