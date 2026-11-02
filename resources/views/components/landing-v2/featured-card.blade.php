{{-- Experience card for the "Go beyond sightseeing" chapter. $lead = large editorial card. --}}
@props(['card', 'lead' => false, 'pairings' => collect()])

@php
    $icons = config('landing_v2.icons');
    $summary = implode(' · ', array_merge($card->tags, $card->features));
@endphp

<article data-admin-record-container {{ $attributes->class([
    'lv-tile relative flex flex-col justify-between',
    'lv-tile--lead min-h-[560px] p-7 lg:min-h-[624px] lg:px-9 lg:pb-9' => $lead,
    'lv-tile--featured aspect-[288/300] p-[18px] sm:p-[22px]' => ! $lead,
 ]) }}>
    <x-site.admin-record kind="experiences" :record-id="$card->id" :label="$card->title" />
    <img src="{{ $card->thumbnailUrl ?: config('landing.images.card_fallback') }}" alt="" loading="lazy" width="{{ $lead ? 576 : 288 }}" height="{{ $lead ? 624 : 300 }}">

    <span class="flex flex-wrap gap-2">
        <span class="lv-chip">{{ $card->city }}@unless($lead) · {{ $card->durationLabel }}@endunless</span>
        @if($lead)<span class="lv-chip">{{ $card->durationLabel }}</span>@endif
    </span>

    <div class="flex flex-col">
        <h3 @class(['lv-serif lv-featured-title', 'max-w-[440px] text-[clamp(2rem,3.4vw,2.75rem)] leading-[1.06]' => $lead, 'text-[25px] leading-[28px]' => ! $lead])>
            <a href="{{ $card->showUrl }}" class="after:absolute after:inset-0">{{ $card->title }}</a>
        </h3>

        @if($lead && $summary !== '')
            <p class="mt-3.5 max-w-[470px] text-base leading-[1.55] text-[color:var(--lv-on-dark-78)]">{{ $summary }}</p>
        @endif

        @if($card->agencyName)
            <p @class(['flex items-center gap-2.5', 'mt-[18px] text-sm text-[color:var(--lv-on-dark)]' => $lead, 'lv-meta mt-2' => ! $lead])>
                @if($lead)<img src="{{ landing_artwork('shield_light', $icons['shield_light']) }}" alt="" width="18" height="18" aria-hidden="true">@endif
                {{ $lead ? (request()->attributes->has('site_content') ? site_content('home.featured.hosted_by', ['agency' => $card->agencyName]) : __('landing_v2.featured.hosted_by', ['agency' => $card->agencyName])) : $card->agencyName }}
            </p>
        @endif

        @if($lead && $pairings->isNotEmpty())
            <p class="relative z-10 mt-5 flex flex-wrap items-center gap-2">
                <span class="me-1 text-[13px] text-[color:var(--lv-on-dark-78)]">{{ (request()->attributes->has('site_content') ? site_content('home.featured.pairs_with') : __('landing_v2.featured.pairs_with')) }}</span>
                @foreach($pairings as $pairing)
                    <span class="lv-chip lv-chip--outline">+ {{ $pairing }}</span>
                @endforeach
            </p>
        @endif

        <div @class(['flex items-center justify-between gap-4', 'mt-6 flex-wrap' => $lead, 'mt-3' => ! $lead])>
            <span class="flex items-center gap-4">
                <span class="lv-rating {{ $lead ? 'text-[15px]' : 'text-sm' }}">
                    <img src="{{ asset($lead ? $icons['star_lg'] : $icons['star_sm']) }}" alt="" width="{{ $lead ? 16 : 15 }}" height="{{ $lead ? 16 : 15 }}" aria-hidden="true">
                    {{ $card->ratingAvg }}
                </span>
                <span @class(['lv-price', 'lv-price--lg' => $lead])>
                    {{ (request()->attributes->has('site_content') ? site_content('home.featured.from') : __('landing_v2.featured.from')) }} <strong>{{ $card->priceFrom }} {{ $card->currency }}</strong>@if($lead) {{ (request()->attributes->has('site_content') ? site_content('home.featured.per_person') : __('landing_v2.featured.per_person')) }}@endif
                </span>
            </span>
            @if($lead)
                <span class="lv-btn lv-btn--clay lv-btn--md" aria-hidden="true">
                    {{ (request()->attributes->has('site_content') ? site_content('home.featured.cta') : __('landing_v2.featured.cta')) }}
                    <img src="{{ landing_artwork('arrow_white', $icons['arrow_white']) }}" alt="" width="20" height="20">
                </span>
            @endif
        </div>
    </div>
</article>
