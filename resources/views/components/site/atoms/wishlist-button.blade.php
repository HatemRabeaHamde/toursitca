{{-- Round heart button on a card image (Figma "Save"). Toggles through the existing wishlist endpoint. --}}
@props(['url', 'saved' => false, 'label' => null])

<button type="button" {{ $attributes->class(['lv-xcard__save']) }} aria-label="{{ $label ?? __('discover.experience.save') }}"
        x-data="wishlistBtn({ isSaved: @js($saved), toggleUrl: @js($url) })" @click.prevent.stop="toggle()"
        :aria-pressed="isSaved.toString()" :class="{ 'is-saved': isSaved }" :disabled="loading">
    <svg class="lv-heart" viewBox="0 0 18 18" aria-hidden="true" focusable="false"><path d="M9 15s-5.6-3.4-6.9-7C1.2 5.5 2.9 3.4 5.3 3.4c1.5 0 2.7.8 3.7 2.2 1-1.4 2.2-2.2 3.7-2.2 2.4 0 4.1 2.1 3.2 4.6C14.6 11.6 9 15 9 15Z"/></svg>
</button>
