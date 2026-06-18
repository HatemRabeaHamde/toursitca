@extends('layouts.site')

@section('content')
    <section class="mx-auto grid max-w-5xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:px-8">
        <x-ui.card :title="__('ui.pages.checkout_activity_title')">
            <p class="text-sm text-slate-600">
                {{ __('ui.messages.checkout_hold_notice', ['minutes' => max(0, now()->diffInMinutes($checkoutSession->reserved_until, false))]) }}
            </p>

            <form method="POST" action="{{ route('site.checkout.activity.store', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" class="mt-6 space-y-4">
                @csrf

                <label class="flex gap-3 rounded-md border border-slate-200 p-4">
                    <input type="radio" name="pickup_status" value="add_now" class="mt-1" @checked(old('pickup_status', $checkoutSession->pickup_status) === 'add_now')>
                    <span>
                        <span class="block text-sm font-semibold text-slate-950">{{ __('ui.checkout.pickup_add_now') }}</span>
                        <span class="mt-1 block text-sm text-slate-600">{{ __('ui.checkout.pickup_add_now_help') }}</span>
                    </span>
                </label>

                <x-ui.input name="pickup_address" :label="__('ui.fields.pickup_address')" :value="old('pickup_address', $checkoutSession->pickup_address)" />

                <div class="grid gap-4 md:grid-cols-2">
                    <x-ui.input name="pickup_lat" type="number" step="0.0000001" :label="__('ui.fields.pickup_lat')" :value="old('pickup_lat', $checkoutSession->pickup_lat)" />
                    <x-ui.input name="pickup_lng" type="number" step="0.0000001" :label="__('ui.fields.pickup_lng')" :value="old('pickup_lng', $checkoutSession->pickup_lng)" />
                </div>

                <label class="flex gap-3 rounded-md border border-slate-200 p-4">
                    <input type="radio" name="pickup_status" value="unknown" class="mt-1" @checked(old('pickup_status', $checkoutSession->pickup_status) === 'unknown')>
                    <span>
                        <span class="block text-sm font-semibold text-slate-950">{{ __('ui.checkout.pickup_unknown') }}</span>
                        <span class="mt-1 block text-sm text-slate-600">{{ __('ui.checkout.pickup_unknown_help') }}</span>
                    </span>
                </label>

                <x-ui.button type="submit" class="w-full justify-center">
                    {{ __('ui.nav.next_contact_details') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <x-ui.card :title="__('ui.pages.order_summary_title')">
            <p class="text-sm font-semibold text-slate-950">{{ $checkoutSession->experience->getTranslation('title', app()->getLocale(), false) }}</p>
            <dl class="mt-4 space-y-3 text-sm text-slate-600">
                <div>
                    <dt class="font-medium text-slate-950">{{ __('ui.fields.availability') }}</dt>
                    <dd>{{ $checkoutSession->availability->date->toDateString() }} · {{ substr($checkoutSession->availability->time_slot, 0, 5) }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-950">{{ __('ui.fields.participants_count') }}</dt>
                    <dd>{{ $checkoutSession->participants_count }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-950">{{ __('ui.fields.total_price') }}</dt>
                    <dd>{{ $checkoutSession->currency }} {{ $checkoutSession->price_snapshot['total'] }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </section>
@endsection
