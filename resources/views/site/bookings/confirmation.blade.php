{{-- Booking confirmation. Data is resolved by BookingRequestPageService. --}}
@extends('layouts.site-v2')

@section('nav_active', 'experiences')

@push('page-styles')
    @vite(['resources/css/pages/account.css'])
@endpush

@php($trip = $page['trip'])

@section('page')
    <div id="content">
        <div class="lv-flow relative overflow-x-clip">
            <x-site.atoms.ornament class="lv-flow__leaves lv-flow__leaves--center" />
            <div class="lv-container lv-sent">
                <span class="lv-flow-tick"><x-site.atoms.icon name="check-dot" :size="28" /></span>
                <x-site.atoms.eyebrow>{{ $page['status'] === 'confirmed' ? __('account.sent.eyebrow') : __('booking.status.'.$page['status']) }}</x-site.atoms.eyebrow>
                <h1 class="lv-t-h1 lv-tone-ink">{{ $page['status'] === 'confirmed' ? __('account.sent.title') : __('instant_booking.status_title') }} <em class="lv-italic">{{ $page['hostName'] }}</em></h1>
                <p class="lv-t-body-l lv-tone-muted lv-sent__lede">{{ $page['status'] === 'confirmed' ? __('account.sent.lede', ['email' => $page['email']]) : __('instant_booking.status_body', ['status' => __('booking.status.'.$page['status'])]) }}</p>
                @if($page['status'] !== 'confirmed')<p class="lv-t-body-s lv-tone-muted">{{ $page['email'] }}</p>@endif

                <div class="lv-sent__card">
                    <div class="flex flex-col gap-4">
                        <h2 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.sent.next') }}</h2>
                        @if($page['status'] === 'confirmed')
                        <ol class="lv-timeline">
                            <li class="is-done"><p class="lv-t-label-m lv-tone-ink">{{ __('account.sent.step1') }}</p><p class="lv-t-body-s lv-tone-muted">{{ __('account.sent.step1_body') }}</p></li>
                            <li>
                                <p class="flex flex-wrap items-center gap-2 lv-t-label-m lv-tone-ink">{{ __('account.sent.step2') }}</p>
                                <p class="lv-t-body-s lv-tone-muted">{{ __('account.sent.step2_body') }}</p>
                            </li>
                            <li><p class="lv-t-label-m lv-tone-muted">{{ __('account.sent.step3') }}</p><p class="lv-t-body-s lv-tone-muted">{{ __('account.sent.step3_body', ['total' => $trip['total']]) }}</p></li>
                            <li><p class="lv-t-label-m lv-tone-muted">{{ __('account.sent.step4') }}</p><p class="lv-t-body-s lv-tone-muted">{{ __('account.sent.step4_body', ['date' => $trip['date']->translatedFormat('M j')]) }}</p></li>
                        </ol>
                        @endif
                    </div>
                    <div class="lv-sent__trip">
                        <img src="{{ $trip['image'] }}" alt="" width="254" height="118" class="lv-flow-summary__image">
                        <p class="lv-t-h5 lv-serif lv-tone-ink">{{ $trip['title'] }}</p>
                        <dl class="lv-kv lv-t-caption">
                            @if($trip['hostLine'])<div><dt>{{ __('account.sent.host') }}</dt><dd>{{ $trip['hostLine'] }}</dd></div>@endif
                            <div><dt>{{ __('account.checkout.dates') }}</dt><dd>{{ $trip['dates'] }}</dd></div>
                            <div><dt>{{ __('experiences.detail.booking.travellers') }}</dt><dd>{{ $trip['travellers'] }} · {{ $trip['group'] }}</dd></div>
                            <div><dt>{{ __('experiences.detail.booking.total') }}</dt><dd>{{ $trip['total'] }}</dd></div>
                        </dl>
                    </div>
                </div>

                <x-site.atoms.button :href="$page['bookingsUrl']">{{ __('account.sent.view') }}</x-site.atoms.button>
                <p class="lv-t-caption lv-tone-muted">{{ __('account.sent.cancel_note') }}</p>
            </div>
        </div>

        @if($page['suggestions'])
            <x-site.organisms.section id="while-you-wait" bg="sand" :divider="true" labelledby="wait-title" class="lv-zellige-end">
                <x-site.molecules.section-header id="wait-title" :eyebrow="__('account.sent.wait_eyebrow')" :before="__('account.sent.wait_title')" :accent="__('account.sent.wait_accent')" />
                <ul class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach($page['suggestions'] as $card)
                        <li class="lv-wait-card">
                            <img src="{{ $card['image'] }}" alt="" width="360" height="200" loading="lazy">
                            <div class="flex flex-1 flex-col items-start gap-2 p-1">
                                <x-site.atoms.eyebrow>{{ $card['eyebrow'] }}</x-site.atoms.eyebrow>
                                <h3 class="lv-t-h5 lv-serif lv-tone-ink">{{ $card['title'] }}</h3>
                                @if($card['body'])<p class="lv-t-body-s lv-tone-muted">{{ $card['body'] }}</p>@endif
                                <x-site.atoms.link-arrow :href="$card['href']" arrow="terracotta" class="mt-auto">{{ $card['link'] }}</x-site.atoms.link-arrow>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </x-site.organisms.section>
        @endif
    </div>
@endsection
