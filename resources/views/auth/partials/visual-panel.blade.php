<div class="auth-visual">
    <a href="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="auth-logo">Rihla<em>Ma</em></a>

    <div class="auth-postcard">
        <img src="https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=1100&q=90" alt="{{ __('ui.auth.visual_alt') }}">
        <div class="auth-postcard__shade"></div>
        <div class="auth-route-mark" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="auth-postcard__caption">
            <span>{{ __('ui.auth.visual_kicker') }}</span>
            <strong>
                {{ __('ui.auth.visual_quote_line_1') }}
                <em>{{ __('ui.auth.visual_quote_line_2') }}</em>
            </strong>
        </div>
    </div>

    <div class="auth-visual__footer">
        <p>{{ __('ui.auth.visual_note') }}</p>
        <div class="auth-trust-list">
            @foreach (__('ui.auth.visual_points') as $point)
                <span>{{ $point }}</span>
            @endforeach
        </div>
    </div>
</div>
