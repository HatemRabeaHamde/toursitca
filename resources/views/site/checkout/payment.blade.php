@extends('layouts.site')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.card :title="__('ui.pages.checkout_payment_title')">
            <p class="text-sm text-slate-600">{{ __('ui.messages.checkout_payment_placeholder') }}</p>
            <dl class="mt-5 space-y-3 text-sm text-slate-600">
                <div>
                    <dt class="font-medium text-slate-950">{{ __('ui.fields.payment_method') }}</dt>
                    <dd>{{ __('ui.payment_methods.'.$checkoutSession->payment_method) }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-950">{{ __('ui.fields.payment_status') }}</dt>
                    <dd>{{ __('ui.payment_status.'.$checkoutSession->payment_status) }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-950">{{ __('ui.fields.total_price') }}</dt>
                    <dd>{{ $checkoutSession->currency }} {{ $checkoutSession->price_snapshot['total'] }}</dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('site.checkout.payment.confirm', ['locale' => app()->getLocale(), 'checkoutSession' => $checkoutSession]) }}" class="mt-6">
                @csrf
                <x-ui.button type="submit" class="w-full justify-center">
                    {{ __('ui.nav.confirm_booking') }}
                </x-ui.button>
            </form>
        </x-ui.card>
    </section>
@endsection
