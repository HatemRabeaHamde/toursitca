@extends('layouts.admin')

@section('title', __('ui.pages.bookings_title'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex flex-wrap items-end gap-3">
            <label class="block">
                <span class="label">{{ __('ui.fields.status') }}</span>
                <select name="status" class="input min-w-40">
                    <option value="">{{ __('ui.fields.all_statuses') }}</option>
                    @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $bookingStatus)
                        <option value="{{ $bookingStatus }}" @selected($status === $bookingStatus)>{{ __('booking.status.'.$bookingStatus) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="label">{{ __('ui.fields.payment_status') }}</span>
                <select name="payment_status" class="input min-w-40">
                    <option value="">{{ __('ui.fields.all_statuses') }}</option>
                    @foreach (['pending', 'not_required', 'paid', 'failed', 'cancelled'] as $statusOption)
                        <option value="{{ $statusOption }}" @selected($paymentStatus === $statusOption)>{{ __('ui.payment_status.'.$statusOption) }}</option>
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
                            <th class="py-3 pr-4">{{ __('ui.fields.agency_name') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.availability') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.booking_type') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.total_price') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.payment_status') }}</th>
                            <th class="py-3 pr-4">{{ __('ui.fields.status') }}</th>
                            <th class="py-3 pr-4"></th>
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
                                <td class="py-3 pr-4 text-slate-600">{{ $booking->experience->agency->name }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ $booking->availability->date->format('Y-m-d') }} · {{ substr($booking->availability->time_slot, 0, 5) }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ __('booking.types.'.$booking->booking_type) }} · {{ $booking->participants_count }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ number_format((float) $booking->total_price, 2) }} MAD</td>
                                <td class="py-3 pr-4">
                                    <form method="POST" action="{{ route('admin.bookings.payment-status.update', $booking) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="payment_status" class="input min-w-32 text-xs">
                                            @foreach (['pending', 'not_required', 'paid', 'failed', 'cancelled'] as $statusOption)
                                                <option value="{{ $statusOption }}" @selected($booking->payment_status === $statusOption)>{{ __('ui.payment_status.'.$statusOption) }}</option>
                                            @endforeach
                                        </select>
                                        <x-ui.button size="sm" type="submit" variant="secondary">{{ __('ui.nav.update') }}</x-ui.button>
                                    </form>
                                </td>
                                <td class="py-3 pr-4">
                                    <x-ui.badge :color="$booking->status === 'confirmed' ? 'green' : ($booking->status === 'cancelled' ? 'red' : 'amber')">
                                        {{ __('booking.status.'.$booking->status) }}
                                    </x-ui.badge>
                                </td>
                                <td class="py-3 pr-4">
                                    @if ($booking->status === 'pending')
                                        <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="flex justify-end">
                                            @csrf
                                            <x-ui.button size="sm" type="submit">{{ __('ui.nav.confirm') }}</x-ui.button>
                                        </form>
                                    @endif
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
