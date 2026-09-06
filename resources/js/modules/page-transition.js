const leaveDuration = 240;

function shouldSkipTransition(event, link) {
    if (
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey
    ) {
        return true;
    }

    if (!link || link.target === '_blank' || link.hasAttribute('download')) {
        return true;
    }

    const url = new URL(link.href, window.location.href);
    const currentUrl = new URL(window.location.href);

    return url.origin !== currentUrl.origin ||
        (url.pathname === currentUrl.pathname && url.search === currentUrl.search && url.hash !== '') ||
        link.dataset.noTransition === 'true';
}

function startProgressBar() {
    const bar = document.createElement('div');
    bar.className = 'page-progress-bar';
    document.body.appendChild(bar);

    requestAnimationFrame(() => {
        bar.style.transform = 'scaleX(0.7)';
    });

    return bar;
}

export function registerPageTransitions() {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion) {
        document.documentElement.classList.add('page-is-ready');
        return;
    }

    document.documentElement.classList.add('page-transition-enabled');

    // Entry: wait a frame so the browser commits the initial (hidden)
    // styles before animating back in — skipping this makes the very
    // first paint jump straight to visible instead of transitioning.
    window.addEventListener('pageshow', (event) => {
        document.documentElement.classList.remove('page-is-leaving');

        // bfcache restores (back/forward) are already painted —
        // re-animating them reads as lag, not smoothness.
        if (event.persisted) {
            document.documentElement.classList.add('page-is-ready');
            return;
        }

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                document.documentElement.classList.add('page-is-ready');
            });
        });
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (shouldSkipTransition(event, link)) {
            return;
        }

        event.preventDefault();

        const destination = link.href;
        document.documentElement.classList.remove('page-is-ready');
        document.documentElement.classList.add('page-is-leaving');

        startProgressBar();

        window.setTimeout(() => {
            window.location.href = destination;
        }, leaveDuration);
    });
}
