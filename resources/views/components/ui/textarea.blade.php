@props(['name', 'label' => null, 'value' => null, 'rows' => 4, 'required' => false, 'help' => null])

@php
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $inputId = str_replace(['[', ']', '.'], ['_', '', '_'], $name);
    $hasError = $errors->has($errorKey);
@endphp

<div class="block">
    @if ($label)
        <label for="{{ $inputId }}" class="label">{{ $label }}@if($required)<span aria-hidden="true" style="color:#B42318;margin-left:3px;">*</span>@endif</label>
    @endif
    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @required($required)
        @if($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
        {{ $attributes->merge(['class' => 'input'.($hasError ? ' is-invalid' : '')]) }}
    >{{ old($errorKey, $value) }}</textarea>
    @if ($help && !$hasError)
        <span class="help">{{ $help }}</span>
    @endif
    @error($errorKey)
        <span id="{{ $inputId }}-error" class="error">{{ $message }}</span>
    @enderror
</div>
