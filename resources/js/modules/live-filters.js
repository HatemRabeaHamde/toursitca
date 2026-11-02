/**
 * Live filters — shared by the Local hosts directory and the Experiences
 * marketplace.
 *
 * The markup is a plain GET form + links (works without JS). This component
 * takes over: it asks the same URL for JSON fragments ({ chips, count,
 * results, more, … }), swaps every `[data-fragment="key"]` in place, glides
 * the results height, staggers new cards in and keeps the URL / back button
 * in sync. `[data-load-more]` appends the next page instead of replacing.
 */
const EASE = 'cubic-bezier(.22, 1, .36, 1)';
const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const modified = (event) => event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;

export function liveFilters({ defaults = {} } = {}) {
    return {
        loading: false,
        appending: false,
        request: null,

        init() {
            // Nested x-data blocks (the sticky search bar) change what `$root` means, so keep the real root.
            this.rootEl = this.$el;
            this.onPopState = () => this.load(window.location.href, { history: 'none', syncForm: true });
            window.addEventListener('popstate', this.onPopState);
        },

        destroy() {
            window.removeEventListener('popstate', this.onPopState);
        },

        /** Chips, "Clear filters", pagination and "Show more" links. */
        navigate(event) {
            if (event.defaultPrevented || modified(event)) return;

            const more = event.target.closest('a[data-load-more]');
            if (more) {
                event.preventDefault();
                this.load(more.href, { history: 'none', append: true });
                return;
            }

            const link = event.target.closest('a[data-filter-link], [data-pagination] a[href]');
            if (!link) return;

            event.preventDefault();
            this.load(link.href, { scroll: link.closest('[data-pagination]') !== null });
        },

        /** Form submit (search box, selects, drawer). Defaults (e.g. sort=recommended) stay out of the URL. */
        submit(form) {
            const url = new URL(form.getAttribute('action') || window.location.href, window.location.href);
            url.search = '';
            new FormData(form).forEach((value, key) => {
                const text = String(value).trim();
                if (text === '' || defaults[key] === text) return;
                url.searchParams.append(key, text);
            });

            const typingOnly = this.withoutKeys(url, ['q', 'search']) === this.withoutKeys(new URL(window.location.href), ['q', 'search']);
            this.load(url.toString(), { history: typingOnly ? 'replace' : 'push' });
            this.$dispatch('live-filters:submitted');
        },

        async load(href, { history = 'push', scroll = false, syncForm = false, append = false } = {}) {
            const url = new URL(href, window.location.href);
            url.hash = '';

            this.request?.abort();
            this.request = new AbortController();
            this.loading = !append;
            this.appending = append;

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.request.signal,
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const parts = await response.json();
                append ? this.append(parts) : this.render(parts);

                if (history !== 'none') {
                    window.history[history === 'replace' ? 'replaceState' : 'pushState']({ live: true }, '', `${url.pathname}${url.search}`);
                }
                if (syncForm) this.syncForm(url);
                if (scroll) this.scrollToTop();
            } catch (error) {
                if (error.name !== 'AbortError') window.location.assign(url);
            } finally {
                this.loading = false;
                this.appending = false;
            }
        },

        fragment(key) {
            return this.rootEl.querySelector(`[data-fragment="${key}"]`);
        },

        render(parts) {
            Object.entries(parts).forEach(([key, html]) => {
                if (key === 'results') return;
                const target = this.fragment(key);
                if (target) target.innerHTML = html;
            });

            const box = this.fragment('results');
            if (!box || parts.results === undefined) return;

            const from = box.offsetHeight;
            box.innerHTML = parts.results;
            if (reducedMotion()) return this.settled();

            this.glide(box, from);
            this.stagger(box.querySelectorAll('[data-card]'));
        },

        /**
         * "Show more": add the next page's cards under the current ones. Several
         * lists (e.g. masonry columns) are matched by their `data-list` value.
         */
        append(parts) {
            const box = this.fragment('results');
            const incoming = new DOMParser().parseFromString(`<div>${parts.results ?? ''}</div>`, 'text/html');
            ['count', 'more'].forEach((key) => {
                const target = this.fragment(key);
                if (target && parts[key] !== undefined) target.innerHTML = parts[key];
            });
            if (!box) return;

            const from = box.offsetHeight;
            const added = [];
            incoming.querySelectorAll('[data-list]').forEach((source, index) => {
                const key = source.dataset.list;
                const target = (key ? box.querySelector(`[data-list="${key}"]`) : null) ?? box.querySelectorAll('[data-list]')[index];
                if (!target) return;
                const cards = [...source.children];
                target.append(...cards);
                added.push(...cards);
            });
            if (!added.length || reducedMotion()) return this.settled();

            this.glide(box, from);
            this.stagger(added.map((card) => (card.matches('[data-card]') ? card : card.querySelector('[data-card]') ?? card)));
        },

        glide(box, from) {
            const to = box.offsetHeight;
            box.style.overflow = 'hidden';
            box.animate([{ height: `${from}px` }, { height: `${to}px` }], { duration: 500, easing: EASE })
                .finished.then(() => this.settled(), () => {});
        },

        stagger(cards) {
            [...cards].forEach((card, index) => {
                card.animate(
                    [{ opacity: 0, transform: 'translateY(18px) scale(.985)' }, { opacity: 1, transform: 'none' }],
                    { duration: 600, delay: Math.min(index, 8) * 55, easing: EASE, fill: 'backwards' },
                );
            });
        },

        /** Let the page (ScrollTrigger, sticky nav) know the layout changed. */
        settled() {
            const box = this.fragment('results');
            if (box) box.style.overflow = '';
            window.dispatchEvent(new CustomEvent('lv:layout'));
        },

        scrollToTop() {
            if (reducedMotion()) this.rootEl.scrollIntoView();
            else window.dispatchEvent(new CustomEvent('lv:scroll-to', { detail: { target: this.rootEl } }));
        },

        /** Back / forward: put the URL's filters back into every named field. */
        syncForm(url) {
            this.rootEl.querySelectorAll('input[name], select[name]').forEach((field) => {
                if (field.type === 'hidden') return;
                const values = url.searchParams.getAll(field.name);
                if (field.type === 'checkbox' || field.type === 'radio') field.checked = values.includes(field.value);
                else field.value = values[0] ?? defaults[field.name] ?? '';
            });
            // Fields that keep their own state (the search bar's menus) re-read the URL.
            window.dispatchEvent(new CustomEvent('live-filters:synced', { detail: { params: url.searchParams } }));
        },

        withoutKeys(url, keys) {
            const copy = new URL(url);
            [...keys, 'page'].forEach((key) => copy.searchParams.delete(key));
            return copy.search;
        },
    };
}
