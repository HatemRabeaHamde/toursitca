{{-- EN / FR / NL pill + dropdown (molecule). Class: App\View\Components\Site\Molecules\LanguageSwitcher --}}
<div {{ $attributes->merge(['class' => 'relative shrink-0']) }} x-data="{ langOpen: false }" @click.outside="langOpen = false" @keydown.escape="langOpen = false">
    <button type="button" class="lv-lang" @click="langOpen = !langOpen" :aria-expanded="langOpen.toString()" aria-haspopup="true">
        <img src="{{ landing_artwork('globe', 'images/site/icons/globe.svg') }}" alt="" width="16" height="16" aria-hidden="true">
        <span class="sr-only">{{ landing_text('site.language.label') }}:</span>
        <span>{{ $currentLabel() }}</span>
        <img src="{{ landing_artwork('chevron', 'images/site/icons/chevron-down-light.svg') }}" alt="" width="14" height="14" aria-hidden="true" class="lv-lang-chevron" :class="{ 'is-open': langOpen }">
    </button>

    <ul class="lv-lang-menu" x-show="langOpen" x-cloak x-transition.origin.top>
        @foreach($locales() as $locale)
            <li>
                <a href="{{ $locale['url'] }}" hreflang="{{ $locale['code'] }}" lang="{{ $locale['code'] }}" @class(['lv-lang-option', 'is-active' => $locale['active']]) @if($locale['active']) aria-current="true" @endif>
                    <span class="lv-t-label-s">{{ $locale['label'] }}</span>
                    <span class="lv-t-caption">{{ $locale['native'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
