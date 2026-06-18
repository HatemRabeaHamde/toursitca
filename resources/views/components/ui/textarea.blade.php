@props(['name', 'label' => null, 'value' => null, 'rows' => 4, 'required' => false])

<label class="block">
    @if ($label)
        <span class="label">{{ $label }}</span>
    @endif
    <textarea name="{{ $name }}" rows="{{ $rows }}" @required($required) {{ $attributes->merge(['class' => 'input']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <span class="error">{{ $message }}</span>
    @enderror
</label>
