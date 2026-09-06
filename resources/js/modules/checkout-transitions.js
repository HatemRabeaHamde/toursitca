const checkoutTransitionMs = 220;

function nativeSubmit(form) {
    HTMLFormElement.prototype.submit.call(form);
}

function resetCheckoutForms() {
    document.documentElement.classList.remove('checkout-is-leaving');

    document.querySelectorAll('[data-checkout-form]').forEach((form) => {
        form.removeAttribute('aria-busy');
        delete form.dataset.checkoutSubmitting;

        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
            button.disabled = false;
        });
    });
}

export function registerCheckoutTransitions() {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    window.addEventListener('pageshow', resetCheckoutForms);

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.matches('[data-checkout-form]')) {
            return;
        }

        if (form.dataset.checkoutSubmitting === 'true') {
            event.preventDefault();
            return;
        }

        if (reduceMotion || !form.checkValidity()) {
            return;
        }

        event.preventDefault();
        form.dataset.checkoutSubmitting = 'true';
        form.setAttribute('aria-busy', 'true');

        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
            button.disabled = true;
        });

        document.documentElement.classList.add('checkout-is-leaving');
        window.setTimeout(() => nativeSubmit(form), checkoutTransitionMs);
    });
}
