{{-- Figma "Card / Story" — a traveller's post-trip story. --}}
@props(['href', 'title', 'image', 'location' => null, 'days' => null, 'rating' => null])

<a href="{{ $href }}" {{ $attributes->class(['lv-scard group']) }}>
    <div class="lv-scard__image">
        <img src="{{ $image }}" alt="" loading="lazy" width="384" height="220">
    </div>
    <div class="lv-scard__body">
        <h3 class="lv-t-h5 lv-tone-ink">{{ $title }}</h3>
        <div class="flex flex-wrap items-center gap-3.5">
            @if($location)<x-site.atoms.meta-item icon="pin">{{ $location }}</x-site.atoms.meta-item>@endif
            @if($days)<x-site.atoms.meta-item icon="calendar">{{ trans_choice('discover.story.days', $days, ['count' => $days]) }}</x-site.atoms.meta-item>@endif
            @if($rating)
                <span class="inline-flex items-center gap-[5px]"><x-site.atoms.icon name="star" :size="14" /><span class="lv-t-label-s lv-tone-ink">{{ $rating }}</span></span>
            @endif
        </div>
        <span class="lv-arrow-link lv-t-label-m lv-tone-terracotta">
            <span>{{ __('discover.story.read') }}</span>
            <x-site.atoms.icon name="arrow-right-terracotta" :size="16" />
        </span>
    </div>
</a>
