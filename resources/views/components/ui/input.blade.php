@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false, 'help' => null])

@php
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $inputId = str_replace(['[', ']', '.'], ['_', '', '_'], $name);
    $hasError = $errors->has($errorKey);
@endphp

<div class="block">
    @if ($label)
        <label for="{{ $inputId }}" class="label">{{ $label }}@if($required)<span aria-hidden="true" style="color:#B42318;margin-left:3px;">*</span>@endif</label>
    @endif
    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($errorKey, $value) }}"
        @required($required)
        @if($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
        {{ $attributes->merge(['class' => 'input'.($hasError ? ' is-invalid' : '')]) }}
    >
    @if ($help && !$hasError)
        <span class="help">{{ $help }}</span>
    @endif
    @error($errorKey)
        <span id="{{ $inputId }}-error" class="error">{{ $message }}</span>
    @enderror
</div>
