{{-- Pill search input. Submits its form after typing stops (Alpine), or on Enter without JS. --}}
@props(['name', 'value' => null, 'placeholder', 'label'])

<label {{ $attributes->class(['lv-search-field']) }}>
    <span class="sr-only">{{ $label }}</span>
    <x-site.atoms.icon name="search" :size="14" />
    <input type="search" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}" maxlength="80"
           class="lv-t-ui-xs" autocomplete="off" @input.debounce.500ms="$el.form.requestSubmit()">
</label>
