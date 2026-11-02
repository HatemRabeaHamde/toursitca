{{-- Side drawer with the less-used filters. Fields belong to #xp-filters via the form attribute. --}}
<template x-teleport="[data-lv-motion]">
    <div class="lv lv-drawer" x-show="open" x-cloak role="dialog" aria-modal="true" aria-labelledby="xp-drawer-title" @keydown.escape="open = false">
        <div class="lv-drawer__backdrop" x-show="open" x-transition.opacity.duration.300ms @click="open = false"></div>
        <div class="lv-drawer__panel" x-show="open" x-trap.inert="open" data-lenis-prevent
             x-transition:enter="lv-drawer-enter" x-transition:enter-start="lv-drawer-from" x-transition:enter-end="lv-drawer-to"
             x-transition:leave="lv-drawer-leave" x-transition:leave-start="lv-drawer-to" x-transition:leave-end="lv-drawer-from">
            <div class="flex items-center justify-between border-b border-[color:var(--lv-line)] px-6 py-5">
                <h2 id="xp-drawer-title" class="lv-t-h4 lv-tone-ink">{{ __('experiences.filters.title') }}</h2>
                <button type="button" class="lv-drawer__close" @click="open = false" aria-label="{{ __('experiences.filters.close') }}">×</button>
            </div>

            <div class="flex flex-1 flex-col gap-7 overflow-y-auto px-6 py-6">
                <label class="flex flex-col gap-2">
                    <span class="lv-t-label-s lv-tone-ink">{{ __('experiences.filters.keyword') }}</span>
                    <input type="search" name="search" form="xp-filters" value="{{ $page->filters->search }}" maxlength="120"
                           placeholder="{{ __('experiences.filters.keyword_placeholder') }}" class="lv-field lv-t-body-s">
                </label>

                <fieldset class="flex flex-col gap-2.5">
                    <legend class="lv-t-label-s lv-tone-ink mb-2.5">{{ __('experiences.filters.duration') }}</legend>
                    <label class="lv-check"><input type="radio" name="duration" value="" form="xp-filters" @checked(! $page->filters->duration)><span class="lv-t-body-s">{{ __('experiences.filters.any_duration') }}</span></label>
                    @foreach($page->durations() as $duration)
                        <label class="lv-check"><input type="radio" name="duration" value="{{ $duration['value'] }}" form="xp-filters" @checked($page->filters->duration === $duration['value'])><span class="lv-t-body-s">{{ $duration['label'] }}</span></label>
                    @endforeach
                </fieldset>

                <fieldset class="flex flex-col gap-2.5">
                    <legend class="lv-t-label-s lv-tone-ink mb-2.5">{{ __('experiences.filters.price') }}</legend>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" name="min_price" form="xp-filters" min="0" step="1" value="{{ $page->filters->minPrice }}" placeholder="{{ __('experiences.filters.min') }}" aria-label="{{ __('experiences.filters.min') }}" class="lv-field lv-t-body-s">
                        <input type="number" name="max_price" form="xp-filters" min="0" step="1" value="{{ $page->filters->maxPrice }}" placeholder="{{ __('experiences.filters.max') }}" aria-label="{{ __('experiences.filters.max') }}" class="lv-field lv-t-body-s">
                    </div>
                </fieldset>

                <fieldset class="flex flex-col gap-2.5">
                    <legend class="lv-t-label-s lv-tone-ink mb-2.5">{{ __('experiences.filters.options') }}</legend>
                    <label class="lv-check"><input type="checkbox" name="private_available" value="1" form="xp-filters" @checked($page->filters->privateAvailable)><span class="lv-t-body-s">{{ __('experiences.filters.private') }}</span></label>
                    <label class="lv-check"><input type="checkbox" name="pickup_available" value="1" form="xp-filters" @checked($page->filters->pickupAvailable)><span class="lv-t-body-s">{{ __('experiences.filters.pickup') }}</span></label>
                </fieldset>
            </div>

            <div class="flex items-center justify-between gap-4 border-t border-[color:var(--lv-line)] px-6 py-4">
                <a href="{{ $page->indexUrl }}#results" class="lv-t-label-m lv-tone-ink underline underline-offset-4" data-filter-link @click="open = false">{{ __('experiences.filters.reset') }}</a>
                <button type="submit" form="xp-filters" class="lv-button lv-button--primary lv-t-label-m">{{ __('experiences.filters.apply') }}</button>
            </div>
        </div>
    </div>
</template>
