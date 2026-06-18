<x-auth.shell
    :show-tabs="false"
    :title="__('ui.auth.verify_title')"
    :subtitle="__('ui.auth.verify_subtitle')"
>
    @if (session('status') == 'verification-link-sent')
        <div class="auth-status">{{ __('ui.auth.verify_link_sent') }}</div>
    @endif

    <div class="auth-panel-note">{{ __('ui.auth.verify_note') }}</div>

    <div class="auth-actions">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="auth-secondary-link">{{ __('ui.auth.log_out') }}</button>
        </form>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="auth-submit" type="submit">{{ __('ui.auth.resend_verification') }}</button>
        </form>
    </div>
</x-auth.shell>
