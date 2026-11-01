{{-- Native <select> skinned as a pill (keyboard + screen-reader friendly). Submits its form on change. --}}
@props(['name', 'options', 'selected' => null, 'label', 'placeholder' => null, 'variant' => 'pill', 'form' => null])

<label {{ $attributes->class(['lv-select', 'lv-select--'.$variant]) }}>
    <span @class(['lv-t-ui-xs lv-tone-muted', 'sr-only' => $variant === 'pill'])>{{ $label }}</span>
    <select name="{{ $name }}" class="lv-t-ui-xs" @if($form) form="{{ $form }}" @endif @change="$el.form.requestSubmit()">
        @if($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
        @foreach($options as $option)
            <option value="{{ $option['value'] }}" @selected($selected === $option['value'])>{{ $option['label'] }}</option>
        @endforeach
    </select>
    <x-site.atoms.icon name="chevron-down" :size="14" />
</label>
