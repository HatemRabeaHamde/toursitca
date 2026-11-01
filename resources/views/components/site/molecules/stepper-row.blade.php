{{--
    Counter row in the travellers popover (Adults / Children).
    value, canDecrease, canIncrease, decrease, increase are Alpine expressions.
--}}
@props(['label', 'hint' => null, 'value', 'canDecrease', 'canIncrease', 'decrease', 'increase'])

<div {{ $attributes->class(['lv-stepper']) }}>
    <div class="flex flex-col">
        <span class="lv-t-label-m lv-tone-ink">{{ $label }}</span>
        @if($hint)<span class="lv-t-caption lv-tone-muted">{{ $hint }}</span>@endif
    </div>
    <div class="lv-stepper__controls">
        <button type="button" class="lv-stepper__btn" :disabled="!({{ $canDecrease }})" @click="{{ $decrease }}"
                aria-label="{{ __('experiences.detail.booking.decrease', ['label' => mb_strtolower($label)]) }}">−</button>
        <span class="lv-t-label-l lv-tone-ink lv-stepper__value" x-text="{{ $value }}" aria-live="polite"></span>
        <button type="button" class="lv-stepper__btn" :disabled="!({{ $canIncrease }})" @click="{{ $increase }}"
                aria-label="{{ __('experiences.detail.booking.increase', ['label' => mb_strtolower($label)]) }}">+</button>
    </div>
</div>
