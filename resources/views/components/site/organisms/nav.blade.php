{{-- Global header (organism). Class: App\View\Components\Site\Organisms\Nav --}}
@php($links = $links())
@php($account = $account())

<header class="lv-header fixed inset-x-0 top-0 z-40" data-lv-header x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="lv-container lv-header-bar flex items-center justify-between gap-6" data-hero-nav data-hero-intro>
        <a href="{{ $homeUrl() }}" class="shrink-0" aria-label="{{ config('brand.name_first') }}{{ config('brand.name_second') }}">
            <img src="{{ landing_artwork('logo_light', config('landing_v2.icons.logo_light')) }}" alt="" width="247" height="44" class="h-9 w-auto xl:h-11">
        </a>

        <nav class="hidden items-center gap-6 lg:flex xl:gap-10" aria-label="{{ landing_text('site.nav.label') }}">
            @foreach($links as $link)
                <a href="{{ $link['href'] }}" @class(['lv-nav-link', 'is-active' => $active === $link['key']]) @if($active === $link['key']) aria-current="page" @endif><span data-content-key="{{ $link['content_key'] }}">{{ $link['label'] }}</span></a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-5 lg:flex xl:gap-7">
            <x-site.molecules.language-switcher />
            <a href="{{ $account['href'] }}" class="lv-nav-link"><span data-content-key="{{ $account['content_key'] }}">{{ $account['label'] }}</span></a>
            <a href="{{ landing_link('nav.link_contact', '#contact') }}" class="lv-btn lv-btn--clay lv-btn--sm"><span data-content-key="nav.contact">{{ landing_text('site.nav.contact') }}</span></a>
        </div>

        <button type="button" class="lv-nav-link p-2 lg:hidden" @click="open = !open" :aria-expanded="open.toString()" aria-controls="lv-mobile-menu">
            <span class="sr-only" x-text="open ? @js(landing_text('site.nav.close')) : @js(landing_text('site.nav.menu'))"><span data-content-key="nav.menu">{{ landing_text('site.nav.menu') }}</span></span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                <path x-show="!open" d="M4 7h16M4 12h16M4 17h16"/>
                <path x-show="open" x-cloak d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
    </div>

    <div id="lv-mobile-menu" class="lv-mobile-menu lg:hidden" x-show="open" x-cloak x-transition.opacity>
        <nav class="lv-container flex flex-col gap-5 pb-8 pt-2" aria-label="{{ landing_text('site.nav.label') }}">
            @foreach($links as $link)
                <a href="{{ $link['href'] }}" @class(['lv-nav-link text-lg', 'is-active' => $active === $link['key']]) @click="open = false"><span data-content-key="{{ $link['content_key'] }}">{{ $link['label'] }}</span></a>
            @endforeach
            <a href="{{ $account['href'] }}" class="lv-nav-link text-lg"><span data-content-key="{{ $account['content_key'] }}">{{ $account['label'] }}</span></a>
            <x-site.molecules.language-switcher class="self-start" />
            <a href="{{ landing_link('nav.link_contact', '#contact') }}" class="lv-btn lv-btn--clay lv-btn--sm self-start" @click="open = false"><span data-content-key="nav.contact">{{ landing_text('site.nav.contact') }}</span></a>
        </nav>
    </div>
</header>
