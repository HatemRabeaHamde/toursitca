{{--
    One destination in the Discover map list. Lives inside x-data="discoverMap(…)":
    hovering / focusing it highlights the matching pin, clicking opens the guide.
--}}
@props(['city'])

<a href="{{ $city['href'] }}" {{ $attributes->class(['lv-city-option group']) }}
   :class="{ 'is-active': isActive(@js($city['slug'])) }"
   @mouseenter="select(@js($city['slug']))" @focus="select(@js($city['slug']))">
    <img src="{{ $city['image'] }}" alt="" loading="lazy" width="64" height="64" class="lv-city-option__image">
    <span class="flex min-w-0 flex-1 flex-col gap-1.5">
        <span class="lv-t-h6 lv-tone-ink">{{ $city['name'] }}</span>
        @if($city['summary'])<span class="lv-t-body-s lv-tone-muted">{{ $city['summary'] }}</span>@endif
        @if($city['tags'])
            <span class="flex flex-wrap gap-1.5 pt-0.5">
                @foreach($city['tags'] as $tag)
                    <x-site.atoms.tag>{{ $tag }}</x-site.atoms.tag>
                @endforeach
            </span>
        @endif
    </span>
    <x-site.atoms.icon name="arrow-right-ink" :size="18" class="lv-city-option__arrow" />
</a>
