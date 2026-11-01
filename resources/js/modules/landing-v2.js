/**
 * Landing v2 — Alpine components.
 * Registered in app.js as `lvHero` and `lvNewsletter`.
 */

/** Auto-advancing hero: crossfades backgrounds and fills the city progress bars. */
export function lvHero({ total = 1, intervalMs = 6000 } = {}) {
    return {
        active: 0,
        total,
        timer: null,

        init() {
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (this.total > 1 && !reducedMotion) {
                this.timer = setInterval(() => this.next(), intervalMs);
            }
        },

        next() {
            this.active = (this.active + 1) % this.total;
        },

        go(index) {
            this.active = index;
        },

        destroy() {
            clearInterval(this.timer);
        },
    };
}

/** Footer newsletter: posts to the existing NewsletterController as JSON. */
export function lvNewsletter({ url, errorMessage }) {
    return {
        email: '',
        status: 'idle', // idle | loading | success | error
        message: '',

        async submit() {
            if (this.status === 'loading') return;
            this.status = 'loading';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({ email: this.email, source: 'landing' }),
                });
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    this.status = 'error';
                    this.message = data.errors?.email?.[0] ?? errorMessage;
                    return;
                }

                this.status = 'success';
                this.message = data.message;
                this.email = '';
            } catch {
                this.status = 'error';
                this.message = errorMessage;
            }
        },
    };
}
