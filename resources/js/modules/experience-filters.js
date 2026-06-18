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
    view: localStorage.getItem('xp-view') === 'list' ? 'list' : 'grid',

    setView(view) {
        this.view = view;
        localStorage.setItem('xp-view', view);
    },

    init() {
        window.addEventListener('popstate', () => this.fetchResults(window.location.href, false));

        document.getElementById('xp-results').addEventListener('click', (event) => {
            const loadMore = event.target.closest('.xp-load-more');
            if (loadMore) {
                this.loadMore(loadMore);
                return;
            }

            // Sort-tab links inside the results panel are plain <a> tags
            // pointing at the same index URL; intercept them too so they
            // swap in place instead of a full reload. Anything else (e.g.
            // experience detail links) navigates normally.
            const link = event.target.closest('.xp-rb-tab');
            if (!link) {
                return;
            }
            event.preventDefault();
            this.fetchResults(link.href, true);
        });
    },

    submit() {
        const url = this.$root.action + '?' + new URLSearchParams(new FormData(this.$root)).toString();
        this.fetchResults(url, true);
    },

    async fetchResults(url, pushState) {
        this.loading = true;

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                window.location.href = url;
                return;
            }

            const html = await response.text();
            const results = document.getElementById('xp-results');
            results.innerHTML = html;
            window.Alpine.initTree(results);

            if (pushState) {
                window.history.pushState({}, '', url);
            }
        } catch {
            window.location.href = url;
        } finally {
            this.loading = false;
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
