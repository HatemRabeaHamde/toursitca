{{-- Labelled text input with its validation error. --}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'errorBag' => 'default', 'optional' => false])

<div class="lv-form-field">
    <label for="{{ $attributes->get('id', $name) }}" class="lv-t-label-s lv-tone-ink">{{ $label }}</label>
    <input id="{{ $attributes->get('id', $name) }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" {{ $attributes->except('id')->class(['lv-form-input']) }}>
    @if($errors->getBag($errorBag)->has($name))<p class="lv-form-error lv-t-caption" role="alert">{{ $errors->getBag($errorBag)->first($name) }}</p>@endif
</div>
