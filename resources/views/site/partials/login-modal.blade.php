<div
    x-data="{ open: false }"
    @open-login-modal.window="open = true"
    x-show="open"
    x-cloak
    class="wl-modal-backdrop"
    @click.self="open = false"
    @keydown.escape.window="open = false"
    role="dialog"
    aria-modal="true"
    aria-labelledby="login-modal-title"
>
    <div class="wl-modal" x-show="open" x-transition>

        <button
            type="button"
            class="wl-modal-close"
            @click="open = false"
            aria-label="Close"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <div class="wl-modal-heart" aria-hidden="true">♥</div>

        <h2 id="login-modal-title" class="wl-modal-title">{{ __('ui.wishlist.modal_title') }}</h2>
        <p class="wl-modal-body">{{ __('ui.wishlist.modal_body') }}</p>

        <div class="wl-modal-actions">
            <a
                href="{{ route('login') }}"
                class="wl-modal-btn wl-modal-btn-primary"
            >{{ __('ui.wishlist.modal_login') }}</a>
            <a
                href="{{ route('register') }}"
                class="wl-modal-btn wl-modal-btn-secondary"
            >{{ __('ui.wishlist.modal_register') }}</a>
        </div>

    </div>
</div>
