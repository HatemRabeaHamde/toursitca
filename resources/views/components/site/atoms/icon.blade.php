{{-- UI icon from public/images/site/icons (exported from Figma). Decorative by default. --}}
@props(['name', 'size' => 16, 'label' => null])

<img src="{{ asset('images/site/icons/'.$name.'.svg') }}" width="{{ $size }}" height="{{ $size }}"
     @if($label) alt="{{ $label }}" @else alt="" aria-hidden="true" @endif
     {{ $attributes->merge(['class' => 'lv-icon shrink-0']) }}>
