@php
    $steps = [
        'activity' => __('ui.checkout.steps.activity'),
        'contact' => __('ui.checkout.steps.contact'),
        'payment' => __('ui.checkout.steps.payment'),
    ];
    $stepKeys = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepKeys, true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
@endphp

<div class="booking-topbar">
    <a href="{{ route('site.home', ['locale' => app()->getLocale()]) }}" class="booking-logo" aria-label="{{ __('ui.nav.home') }}">
        Toursti<em>Ca</em>
    </a>

    <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="booking-back">
        {{ __('ui.nav.back_to_experiences') }}
    </a>
</div>

<nav class="booking-steps" aria-label="{{ __('ui.checkout.steps_label') }}">
    @foreach ($steps as $key => $label)
        @php $index = array_search($key, $stepKeys, true); @endphp
        <div class="booking-step @if ($index === $currentIndex) is-active @elseif ($index < $currentIndex) is-complete @endif">
            <span>{{ $index + 1 }}</span>
            {{ $label }}
        </div>
    @endforeach
</nav>
