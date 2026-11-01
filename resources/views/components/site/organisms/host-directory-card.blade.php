{{-- Local hosts directory card: portrait, name + rating, role · place, licence, experience count. --}}
@props(['href', 'name', 'role', 'image', 'rating' => null, 'reviews' => null, 'licence' => null, 'experiences' => 0])

<article {{ $attributes->class(['lv-dcard group']) }}>
    <div class="lv-dcard__image">
        <img src="{{ $image }}" alt="{{ $name }}" loading="lazy" width="384" height="300">
    </div>
    <div class="lv-dcard__body">
        <div class="flex items-start justify-between gap-3">
            <h3 class="lv-t-h5 lv-tone-ink"><a href="{{ $href }}" class="lv-dcard__link">{{ $name }}</a></h3>
            @if($rating)
                <x-site.atoms.rating :value="$rating" :count="$reviews" size="s" class="shrink-0 pt-1"
                    aria-label="{{ __('hosts.card.rating', ['rating' => $rating, 'count' => $reviews]) }}" />
            @endif
        </div>
        <p class="lv-t-body-s lv-tone-muted">{{ $role }}</p>
        @if($licence)
            <x-site.atoms.verified-badge>{{ __('hosts.card.licensed', ['number' => $licence]) }}</x-site.atoms.verified-badge>
        @endif
        <div class="lv-dcard__foot">
            <span class="lv-t-caption lv-tone-muted">{{ $experiences ? trans_choice('hosts.card.experiences', $experiences, ['count' => $experiences]) : __('hosts.card.new') }}</span>
            <span class="lv-arrow-link lv-t-label-s lv-tone-terracotta">
                <span>{{ __('hosts.card.view_profile') }}</span>
                <x-site.atoms.icon name="arrow-right-terracotta" :size="14" />
            </span>
        </div>
    </div>
</article>
