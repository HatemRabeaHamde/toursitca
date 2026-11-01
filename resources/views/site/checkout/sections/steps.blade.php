{{-- The four steps of a request. $current = the active step (1–4). --}}
<ol class="lv-steps" aria-label="{{ __('account.checkout.steps_label') }}">
    @foreach(['details', 'sent', 'confirms', 'meeting'] as $step)
        <li @class(['lv-steps__step lv-t-ui-s', 'is-current' => $loop->iteration === $current]) @if($loop->iteration === $current) aria-current="step" @endif>
            <span class="lv-steps__dot">{{ $loop->iteration }}</span>{{ __('account.checkout.steps.'.$step) }}
        </li>
    @endforeach
</ol>
