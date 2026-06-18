@extends('layouts.admin')

@section('title', __('ui.pages.admin_title'))

@section('content')
    <section class="space-y-6">
        <div class="rounded-md border border-slate-200 bg-white p-5">
            <h2 class="text-xl font-semibold text-slate-950">{{ __('ui.pages.admin_title') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('ui.pages.admin_intro') }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <x-ui.button :href="route('admin.agencies.index')">{{ __('ui.nav.agencies') }}</x-ui.button>
                <x-ui.button :href="route('admin.bookings.index')" variant="secondary">{{ __('ui.nav.bookings') }}</x-ui.button>
                <x-ui.button :href="route('admin.experiences.index')" variant="secondary">{{ __('ui.nav.experiences') }}</x-ui.button>
                <x-ui.button :href="route('admin.availability.index')" variant="secondary">{{ __('ui.nav.availability') }}</x-ui.button>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <x-ui.stat-card :label="__('ui.dashboard.agencies_total')" :value="$stats['agencies_total']" />
            <x-ui.stat-card :label="__('ui.dashboard.agencies_pending')" :value="$stats['agencies_pending']" />
            <x-ui.stat-card :label="__('ui.dashboard.experiences_total')" :value="$stats['experiences_total']" />
            <x-ui.stat-card :label="__('ui.dashboard.experiences_published')" :value="$stats['experiences_published']" />
            <x-ui.stat-card :label="__('ui.dashboard.bookings_pending')" :value="$stats['bookings_pending']" />
            <x-ui.stat-card :label="__('ui.dashboard.bookings_confirmed')" :value="$stats['bookings_confirmed']" />
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-ui.card :title="__('ui.dashboard.bookings_created_trend')">
                @php($maxCreated = max(1, $bookingsCreatedTrend->max('value')))
                <div class="grid grid-cols-7 gap-2">
                    @foreach ($bookingsCreatedTrend as $item)
                        <div class="text-center">
                            <div class="flex h-24 items-end justify-center">
                                <div class="w-7 rounded-t bg-slate-300" style="height: {{ (int) (($item['value'] / $maxCreated) * 96) }}px"></div>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $item['label'] }}</p>
                            <p class="text-xs font-medium text-slate-700">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card :title="__('ui.dashboard.bookings_confirmed_trend')">
                @php($maxConfirmed = max(1, $bookingsConfirmedTrend->max('value')))
                <div class="grid grid-cols-7 gap-2">
                    @foreach ($bookingsConfirmedTrend as $item)
                        <div class="text-center">
                            <div class="flex h-24 items-end justify-center">
                                <div class="w-7 rounded-t bg-emerald-400" style="height: {{ (int) (($item['value'] / $maxConfirmed) * 96) }}px"></div>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $item['label'] }}</p>
                            <p class="text-xs font-medium text-slate-700">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-ui.card :title="__('ui.dashboard.recent_bookings')">
                @include('partials.dashboard.recent-bookings', ['bookings' => $recentBookings, 'showAgency' => true])
            </x-ui.card>

            <x-ui.card :title="__('ui.dashboard.pending_agencies')">
                @if ($pendingAgencies->isEmpty())
                    <x-ui.empty-state :title="__('ui.messages.no_agencies')" />
                @else
                    <div class="space-y-3">
                        @foreach ($pendingAgencies as $agency)
                            <div class="rounded-md border border-slate-200 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-slate-950">{{ $agency->name }}</p>
                                    <x-ui.badge color="amber">{{ __('ui.status.pending') }}</x-ui.badge>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">{{ $agency->user->email }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-ui.card :title="__('ui.dashboard.pending_agencies_attention')">
                @if ($pendingAgenciesAged->isEmpty())
                    <x-ui.empty-state :title="__('ui.dashboard.no_pending_agencies_attention')" />
                @else
                    <div class="space-y-3">
                        @foreach ($pendingAgenciesAged as $agency)
                            <div class="rounded-md border border-amber-200 bg-amber-50 p-3">
                                <p class="text-sm font-semibold text-amber-900">{{ $agency->name }}</p>
                                <p class="mt-1 text-xs text-amber-800">{{ $agency->user->email }}</p>
                                <p class="mt-1 text-xs text-amber-800">{{ __('ui.dashboard.pending_since') }}: {{ $agency->created_at->format('Y-m-d') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card :title="__('ui.dashboard.almost_full_slots')">
                @if ($almostFullSlots->isEmpty())
                    <x-ui.empty-state :title="__('ui.dashboard.no_almost_full_slots')" />
                @else
                    <div class="space-y-3">
                        @foreach ($almostFullSlots as $slot)
                            <div class="rounded-md border border-slate-200 p-3">
                                <p class="text-sm font-semibold text-slate-950">{{ $slot->experience->getTranslation('title', app()->getLocale(), false) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $slot->experience->agency->name }} · {{ $slot->date->format('Y-m-d') }} · {{ substr($slot->time_slot, 0, 5) }}</p>
                                <p class="mt-1 text-xs font-medium text-slate-700">{{ __('ui.fields.available_seats') }}: {{ $slot->availableSeats() }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>
    </section>
@endsection
