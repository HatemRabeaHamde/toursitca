/**
 * Experiences listing filters.
 *
 * One GET form wraps the search bar and the sidebar. Any change auto-submits
 * the whole form, so search + every sidebar filter stay in sync. The search
 * input debounces in the template; everything else submits on change.
 *
 * Submission is done via fetch() to avoid a full page reload: the server
 * returns just the results partial (#xp-results contents), which we swap in,
 * then sync the URL with history.pushState. Browser back/forward (popstate)
 * re-fetches for the restored URL. Direct navigation / no-JS still works via
 * the form's normal GET submission.
 */
export const experienceFilters = () => ({
    filters: true, // sidebar visible
    loading: false,
    view: 'grid',
    controller: null,

    setView(view) {
        this.view = view;
    },

    init() {
        window.addEventListener('popstate', () => this.fetchPage(window.location.href, false));

        this.$root.addEventListener('click', (event) => {
            const loadMore = event.target.closest('.xp-load-more');
            if (loadMore) {
                event.preventDefault();
                this.loadMore(loadMore);
                return;
            }

            const link = event.target.closest('a[href]');
            if (!this.isFilterLink(link)) {
                return;
            }

            event.preventDefault();
            this.fetchPage(link.href, true);
        });
    },

    submit() {
        this.fetchPage(this.formUrl(), true);
    },

    isFilterLink(link) {
        if (!link || link.origin !== window.location.origin) {
            return false;
        }

        const filterSelectors = [
            '.xp-chip',
            '.xp-af-chip a',
            '.xp-af-clear',
            '.xp-reset',
            '.xp-range-pill',
            '.xp-rb-tab',
        ].join(',');

        return Boolean(link.closest(filterSelectors));
    },

    formUrl() {
        const url = new URL(this.$root.action, window.location.origin);
        const params = new URLSearchParams(new FormData(this.$root));

        [...params.entries()].forEach(([key, value]) => {
            if (value === '' || value === null) {
                params.delete(key);
            }
        });
        params.delete('page');
        if (params.get('sort') === 'recommended') {
            params.delete('sort');
        }

        url.search = params.toString();

        return url.toString();
    },

    async fetchPage(url, pushState) {
        if (this.controller) {
            this.controller.abort();
        }

        const controller = new AbortController();
        this.controller = controller;
        this.loading = true;

        try {
            const response = await fetch(url, {
                headers: { Accept: 'text/html' },
                signal: controller.signal,
            });

            if (!response.ok) {
                window.location.href = url;
                return;
            }

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const nextForm = doc.querySelector('[data-experience-filters]');

            if (!nextForm) {
                window.location.href = url;
                return;
            }

            this.$root.innerHTML = nextForm.innerHTML;
            window.Alpine.initTree(this.$root);

            if (pushState) {
                window.history.pushState({}, '', url);
            }
        } catch {
            if (controller.signal.aborted) {
                return;
            }

            window.location.href = url;
        } finally {
            if (this.controller === controller) {
                this.loading = false;
                this.controller = null;
            }
        }
    },

    /**
     * "Load more" appends the next page's cards into the existing grid
     * instead of replacing #xp-results, and swaps in the next page's
     * pagination footer (next "Load more" button, or nothing if last page).
     */
    async loadMore(button) {
        this.loading = true;
        button.disabled = true;

        try {
            if (this.controller) {
                this.controller.abort();
            }

            const response = await fetch(button.dataset.nextUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                return;
            }

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');

            const grid = document.querySelector('#xp-results .xp-grid');
            const newCards = doc.querySelectorAll('.xp-grid > .xc');
            newCards.forEach((card) => grid.appendChild(card));

            const pagination = document.querySelector('#xp-results .xp-pagination');
            const newPagination = doc.querySelector('.xp-pagination');
            if (pagination) {
                if (newPagination) {
                    pagination.replaceWith(newPagination);
                } else {
                    pagination.remove();
                }
            }

            window.history.pushState({}, '', button.dataset.nextUrl);
        } finally {
            this.loading = false;
        }
    },

    /**
     * Keep a checkbox group single-select (checkbox look, radio behaviour) —
     * used for Duration, which the backend treats as a single value.
     */
    onlyOne(event) {
        if (!event.target.checked) {
            return;
        }
        this.$root
            .querySelectorAll(`input[name="${event.target.name}"]`)
            .forEach((box) => {
                if (box !== event.target) {
                    box.checked = false;
                }
            });
    },
});
