{{-- The Tourstica logo from the landing page. variant: "light" for dark backgrounds, "dark" for light ones. --}}
@props(['variant' => 'light', 'height' => 32])

<img src="{{ asset('images/brand/logo-'.$variant.'.svg') }}" alt="{{ config('app.name') }}" height="{{ $height }}" {{ $attributes->merge(['style' => 'height:'.$height.'px;width:auto;display:block']) }}>
