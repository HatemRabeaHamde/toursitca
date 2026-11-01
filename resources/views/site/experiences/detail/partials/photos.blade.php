{{-- Full-screen photo viewer opened from the hero ("View all N photos"). Uses photoViewer on #content. --}}
<template x-teleport="[data-lv-motion]">
    <div class="lv-dialog lv-dialog--dark" x-show="open" x-cloak x-transition.opacity.duration.300ms
         @keydown.escape.window="close()" @keydown.arrow-right.window="open && next()" @keydown.arrow-left.window="open && prev()"
         role="dialog" aria-modal="true" aria-label="{{ __('experiences.detail.gallery.title') }}" data-lenis-prevent>
        <div class="lv-viewer" x-trap.noscroll="open" @click.self="close()">
            <div class="lv-viewer__bar">
                <p class="lv-t-label-s lv-tone-cream" x-text="@js(__('experiences.detail.gallery.counter', ['current' => '#c', 'total' => '#t'])).replace('#c', current + 1).replace('#t', photos.length)"></p>
                <button type="button" class="lv-dialog__close lv-dialog__close--light" @click="close()" aria-label="{{ __('experiences.detail.gallery.close') }}">×</button>
            </div>
            <img class="lv-viewer__image" :src="photo" alt="" x-show="photo">
            <template x-if="photos.length > 1">
                <div class="lv-viewer__nav">
                    <button type="button" class="lv-viewer__arrow" @click="prev()" aria-label="{{ __('experiences.detail.gallery.previous') }}">‹</button>
                    <button type="button" class="lv-viewer__arrow" @click="next()" aria-label="{{ __('experiences.detail.gallery.next') }}">›</button>
                </div>
            </template>
        </div>
    </div>
</template>
