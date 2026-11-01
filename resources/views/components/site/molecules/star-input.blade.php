{{-- Accessible 1–5 star picker (radio group). Lives inside an Alpine scope through its own x-data. --}}
@props(['name', 'label', 'value' => 0, 'size' => 22, 'words' => null])

<fieldset class="lv-stars" x-data="{ value: {{ (int) $value }}, words: @js($words) }">
    <legend class="sr-only">{{ $label }}</legend>
    @for($star = 1; $star <= 5; $star++)
        <label class="lv-stars__star">
            <input type="radio" name="{{ $name }}" value="{{ $star }}" class="sr-only" x-model.number="value" @checked((int) $value === $star)>
            <span class="sr-only">{{ trans_choice('account.review.stars', $star, ['count' => $star]) }}</span>
            <img src="{{ asset('images/site/icons/star.svg') }}" alt="" width="{{ $size }}" height="{{ $size }}" x-show="value >= {{ $star }}" x-cloak>
            <img src="{{ asset('images/site/icons/star-off.svg') }}" alt="" width="{{ $size }}" height="{{ $size }}" x-show="value < {{ $star }}">
        </label>
    @endfor
    @if($words)<span class="lv-stars__word lv-t-h4 lv-serif" x-text="words[value] || ''"></span>@endif
</fieldset>
