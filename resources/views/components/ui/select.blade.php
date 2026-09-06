@props(['name', 'label' => null, 'options' => [], 'selected' => null, 'required' => false, 'help' => null, 'placeholder' => null])

@php
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $inputId = str_replace(['[', ']', '.'], ['_', '', '_'], $name);
    $hasError = $errors->has($errorKey);
@endphp

<div class="block">
    @if ($label)
        <label for="{{ $inputId }}" class="label">{{ $label }}@if($required)<span aria-hidden="true" style="color:#B42318;margin-left:3px;">*</span>@endif</label>
    @endif
    <select
        id="{{ $inputId }}"
        name="{{ $name }}"
        @required($required)
        @if($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
        {{ $attributes->merge(['class' => 'input'.($hasError ? ' is-invalid' : '')]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected(old($errorKey, $selected) == $value)>{{ $text }}</option>
        @endforeach
    </select>
    @if ($help && !$hasError)
        <span class="help">{{ $help }}</span>
    @endif
    @error($errorKey)
        <span id="{{ $inputId }}-error" class="error">{{ $message }}</span>
    @enderror
</div>
