{{-- Figma "Partner card": round portrait, name ★ rating (reviews), role · place, licence, experience count, arrow. --}}
@props(['href', 'name', 'role', 'avatar', 'rating' => null, 'reviews' => null, 'licence' => null, 'experiences' => 0])

<a href="{{ $href }}" {{ $attributes->class(['lv-pcard group']) }}>
    <img src="{{ $avatar }}" alt="" width="72" height="72" loading="lazy" class="lv-pcard__avatar">
    <span class="flex min-w-0 flex-1 flex-col gap-1">
        <span class="flex flex-wrap items-center gap-1.5">
            <span class="lv-t-h5 lv-tone-ink">{{ $name }}</span>
            @if($rating)
                <x-site.atoms.icon name="star-13" :size="13" />
                <span class="lv-t-label-s lv-tone-ink">{{ $rating }}</span>
                @if($reviews)<span class="lv-t-caption lv-tone-muted">({{ $reviews }})</span>@endif
            @endif
        </span>
        <span class="lv-t-body-s lv-tone-muted truncate">{{ $role }}</span>
        @if($licence)
            <span class="inline-flex items-center gap-1.5">
                <x-site.atoms.icon name="shield-success-14" :size="14" />
                <span class="lv-t-label-xs lv-tone-success">{{ __('hosts.card.licensed', ['number' => $licence]) }}</span>
            </span>
        @endif
        <span class="lv-t-caption lv-tone-muted">{{ $experiences ? trans_choice('hosts.card.experiences', $experiences, ['count' => $experiences]) : __('hosts.card.new') }}</span>
    </span>
    <x-site.atoms.icon name="arrow-right-terracotta-18" :size="18" class="lv-pcard__arrow" />
</a>
