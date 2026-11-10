{{--
    Coordinate picker: place search + real map + pin readout (Alpine coordinatePicker, resources/js/modules/maps).
    $options  JS object entries passed to coordinatePicker (inputs/onPick/variant/lazy)
    $hint     helper line above the map
    $lazy     true = collapsed behind a toggle (itinerary stops); false = built on first sight
    $route    true = also show the itinerary stops as faded context (meeting-point map)
--}}
@php
    $lazy = $lazy ?? false;
    $route = $route ?? false;
    $labels = [
        'noResults' => __('ui.map.no_results'),
        'searchFailed' => __('ui.map.search_failed'),
        'stop' => __('ui.map.stop'),
    ];
@endphp

<div @class(['ts-picker', 'ts-picker--compact' => $lazy])
     x-data="coordinatePicker({ {{ $options }}, lazy: @js($lazy), labels: @js($labels) })"
     @unless($lazy) x-intersect.once="load()" @endunless
     @if($route) @xp-route-changed.window="setRoute($event.detail)" @endif>
    @if($lazy)
        <button type="button" class="xp-map-toggle" @click="toggle()" :aria-expanded="open.toString()">
            <span x-text="open ? @js(__('ui.map.hide_stop_map')) : @js(__('ui.map.show_stop_map'))"></span>
            <span class="ts-picker__chip" x-show="hasPoint" x-text="readout" x-cloak></span>
        </button>
    @endif

    <div class="ts-picker__body" x-show="open" @if($lazy) x-cloak @endif>
        <p class="xp-map-hint">{{ $hint }}</p>

        <div class="ts-picker__search">
            <label class="ts-picker__field">
                <span class="sr-only">{{ __('ui.map.search_label') }}</span>
                <input type="search" class="input ts-picker__input" x-model="query" autocomplete="off"
                       placeholder="{{ __('ui.map.search_placeholder') }}"
                       @keydown.enter.prevent="search()" @keydown.escape="results = []">
            </label>
            <button type="button" class="ts-picker__go" @click="search()" :disabled="searching">
                <span x-text="searching ? @js(__('ui.map.searching')) : @js(__('ui.map.search'))"></span>
            </button>
            <ul class="ts-picker__results" x-show="results.length" x-cloak @click.outside="results = []">
                <template x-for="result in results" :key="result.id">
                    <li><button type="button" @click="choose(result)" x-text="result.name"></button></li>
                </template>
            </ul>
        </div>
        <p class="ts-picker__message" x-show="message" x-text="message" role="status" x-cloak></p>

        <div x-ref="canvas" class="ts-picker__canvas" role="application" aria-label="{{ __('ui.map.canvas_label') }}"></div>

        <div class="ts-picker__foot">
            <span x-show="hasPoint">{{ __('ui.map.pinned') }} <strong x-text="readout"></strong></span>
            <span x-show="!hasPoint">{{ __('ui.map.no_pin') }}</span>
            <button type="button" class="ts-picker__clear" x-show="hasPoint" @click="clear()">{{ __('ui.map.clear') }}</button>
        </div>
    </div>
</div>
