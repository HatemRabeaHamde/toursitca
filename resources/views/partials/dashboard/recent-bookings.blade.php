@props([
    'bookings',
    'showAgency' => false,
])

@if ($bookings->isEmpty())
    <x-ui.empty-state :title="__('ui.messages.no_bookings')" />
@else
    <div class="space-y-3">
        @foreach ($bookings as $booking)
            <div class="rounded-md border border-slate-200 p-3">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-950">#{{ $booking->id }} · {{ $booking->guest_name }}</p>
                    <x-ui.badge :color="$booking->status === 'confirmed' ? 'green' : ($booking->status === 'cancelled' ? 'red' : 'amber')">
                        {{ __('booking.status.'.$booking->status) }}
                    </x-ui.badge>
                </div>
                <p class="mt-1 text-sm text-slate-600">{{ $booking->experience->getTranslation('title', app()->getLocale(), false) }}</p>
                <p class="mt-1 text-xs text-slate-500">
                    @if ($showAgency)
                        {{ $booking->experience->agency->name }} ·
                    @endif
                    {{ $booking->availability->date->format('Y-m-d') }} · {{ substr($booking->availability->time_slot, 0, 5) }}
                </p>
            </div>
        @endforeach
    </div>
@endif
