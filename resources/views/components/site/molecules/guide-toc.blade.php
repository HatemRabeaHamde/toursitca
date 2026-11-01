{{--
    Sticky "In this guide" nav for long guide pages. Highlights the chapter in view (Alpine guideToc).
    links: [['id' => 'overview', 'label' => 'Overview'], …]
--}}
@props(['title', 'links'])

<nav {{ $attributes->class(['lv-toc']) }} aria-label="{{ $title }}"
     x-data="guideToc(@js(array_column($links, 'id')))">
    <p class="lv-t-eyebrow-s lv-tone-muted">{{ $title }}</p>
    <ul class="mt-4 flex flex-col gap-1">
        @foreach($links as $link)
            <li>
                <a href="#{{ $link['id'] }}" @class(['lv-toc__link lv-t-ui-xs', 'is-active' => $loop->first])
                   :class="{ 'is-active': active === @js($link['id']) }"
                   :aria-current="active === @js($link['id']) ? 'true' : null">{{ $link['label'] }}</a>
            </li>
        @endforeach
    </ul>
    {{ $slot }}
</nav>
