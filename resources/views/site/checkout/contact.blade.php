@extends('layouts.site')

@section('content')
    <section class="mx-auto grid max-w-5xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:px-8">
        <x-ui.card :title="__('ui.pages.checkout_contact_title')">
            <form method="POST" action="{{ route('site.checkout.contact.store', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" class="space-y-4">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <x-ui.input name="contact_first_name" :label="__('ui.fields.contact_first_name')" :value="old('contact_first_name', $checkoutSession->contact_first_name ?? auth()->user()?->name)" required />
                    <x-ui.input name="contact_last_name" :label="__('ui.fields.contact_last_name')" :value="old('contact_last_name', $checkoutSession->contact_last_name)" required />
                </div>

                <x-ui.input name="contact_email" type="email" :label="__('ui.fields.contact_email')" :value="old('contact_email', $checkoutSession->contact_email ?? auth()->user()?->email)" required />
                <x-ui.input name="contact_phone" :label="__('ui.fields.contact_phone')" :value="old('contact_phone', $checkoutSession->contact_phone)" />
                <x-ui.input name="contact_country" :label="__('ui.fields.contact_country')" :value="old('contact_country', $checkoutSession->contact_country)" />
                <x-ui.textarea name="special_requests" :label="__('ui.fields.special_requests')" :value="old('special_requests', $checkoutSession->special_requests)" />

                <x-ui.button type="submit" class="w-full justify-center">
                    {{ __('ui.nav.next_payment') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <x-ui.card :title="__('ui.pages.order_summary_title')">
            <p class="text-sm font-semibold text-slate-950">{{ $checkoutSession->experience->getTranslation('title', app()->getLocale(), false) }}</p>
            <dl class="mt-4 space-y-3 text-sm text-slate-600">
                <div>
                    <dt class="font-medium text-slate-950">{{ __('ui.fields.pickup_status') }}</dt>
                    <dd>{{ __('ui.pickup_status.'.$checkoutSession->pickup_status) }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-950">{{ __('ui.fields.total_price') }}</dt>
                    <dd>{{ $checkoutSession->currency }} {{ $checkoutSession->price_snapshot['total'] }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </section>
@endsection
