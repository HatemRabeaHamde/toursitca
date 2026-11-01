{{-- Sticky section tabs with scroll-spy (Overview · Itinerary · Host · Reviews). --}}
@php($tabs = $page->tabs())

<nav class="lv-xtabs" aria-label="{{ __('experiences.detail.tabs.label') }}" x-data="guideToc(@js(array_column($tabs, 'id')))">
    <div class="lv-container lv-xtabs__inner">
        @foreach($tabs as $tab)
            <a href="#{{ $tab['id'] }}" @class(['lv-xtabs__link', 'is-active' => $loop->first])
               :class="{ 'is-active': active === @js($tab['id']) }"
               :aria-current="active === @js($tab['id']) ? 'true' : null">{{ $tab['label'] }}</a>
        @endforeach
    </div>
</nav>
