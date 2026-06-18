@props(['name', 'label' => null, 'options' => [], 'selected' => null, 'required' => false])

<label class="block">
    @if ($label)
        <span class="label">{{ $label }}</span>
    @endif
    <select name="{{ $name }}" @required($required) {{ $attributes->merge(['class' => 'input']) }}>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <span class="error">{{ $message }}</span>
    @enderror
</label>
