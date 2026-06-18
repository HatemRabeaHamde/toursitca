@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false])

<label class="block">
    @if ($label)
        <span class="label">{{ $label }}</span>
    @endif
    <input
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        @required($required)
        {{ $attributes->merge(['class' => 'input']) }}
    >
    @error($name)
        <span class="error">{{ $message }}</span>
    @enderror
</label>
