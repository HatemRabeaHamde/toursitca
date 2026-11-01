{{-- Decorative leaves overlay. Placement comes from the section via class (top-right, top-center…). --}}
@props(['flip' => false])

<img src="{{ asset('images/site/ornaments/leaves.png') }}" alt="" aria-hidden="true" loading="lazy" width="520" height="347"
     {{ $attributes->class(['lv-ornament-leaves', 'is-flipped' => $flip]) }}>
