{{-- Section tabs with scroll-spy, sticky at the top of the main column. --}}
@php($tabs = $page->tabsV1())

<nav class="lv-v1-tabs" aria-label="{{ __('experiences.detail.tabs.label') }}" x-data="guideToc(@js(array_column($tabs, 'id')))">
    @foreach($tabs as $tab)
        <a href="#{{ $tab['id'] }}" @class(['lv-xtabs__link', 'is-active' => $loop->first, 'lv-v1-desktop-only' => $tab['id'] === 'options'])
           :class="{ 'is-active': active === @js($tab['id']) }"
           :aria-current="active === @js($tab['id']) ? 'true' : null">{{ $tab['label'] }}</a>
    @endforeach
</nav>
