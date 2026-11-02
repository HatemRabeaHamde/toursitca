{{-- Traveller story. Data: managed Testimonials (post-trip reviews). --}}
@props(['story'])

@php
    $icons = config('landing_v2.icons');
    $bookUrl = route('site.experiences.index', ['locale' => app()->getLocale(), 'search' => $story['experience_title']]);
@endphp

<article data-admin-record-container class="lv-card flex h-full flex-col overflow-hidden">
    <x-site.admin-record kind="testimonials" :record-id="$story['id'] ?? null" :label="$story['author_name']" />
    <img src="{{ $story['cover_url'] }}" alt="{{ $story['cover_alt'] ?? '' }}" loading="lazy" width="384" height="140" class="h-[140px] w-full object-cover">

    <div class="flex-1 px-[26px] py-[22px]">
        <p class="flex gap-[3px]" role="img" aria-label="{{ (request()->attributes->has('site_content') ? site_content('home.stories.rating_label', ['rating'=>$story['rating']]) : __('landing_v2.stories.rating_label', ['rating'=>$story['rating']])) }}">
            @for($i = 0; $i < $story['stars']; $i++)
                <img src="{{ landing_artwork('star_gold', $icons['star_gold']) }}" alt="" width="17" height="17">
            @endfor
        </p>
        <blockquote class="lv-story-quote mt-3">“{{ $story['body'] }}”</blockquote>
        <p class="mt-4 text-sm">
            <strong class="font-bold text-[color:var(--lv-ink)]">{{ $story['author_name'] }}@if($story['author_country']), {{ $story['author_country'] }}@endif</strong>
            @if($story['experience_title'])<span class="text-[color:var(--lv-muted)]"> · {{ $story['experience_title'] }}</span>@endif
        </p>
    </div>

    <div class="lv-story-foot flex min-h-[60px] items-center justify-between gap-4 px-[26px] py-3">
        <a href="{{ $bookUrl }}" class="lv-story-read inline-flex shrink-0 items-center gap-2">
            {{ (request()->attributes->has('site_content') ? site_content('home.stories.read') : __('landing_v2.stories.read')) }}
            <img src="{{ landing_artwork('arrow_clay_sm', $icons['arrow_clay_sm']) }}" alt="" width="17" height="17" aria-hidden="true">
        </a>
        <a href="{{ $bookUrl }}" class="text-end text-sm text-[color:var(--lv-muted)] hover:text-[color:var(--lv-ink)]">{{ (request()->attributes->has('site_content') ? site_content('home.stories.cta') : __('landing_v2.stories.cta')) }}</a>
    </div>
</article>
