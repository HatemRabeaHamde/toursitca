/**
 * Alpine components for the Discover pages.
 */

/**
 * Map ↔ list sync on the Discover hub: hovering or focusing a pin or a city
 * row selects it, which highlights both and shows that city's preview card.
 */
export function discoverMap({ initial = null } = {}) {
    return {
        active: initial,

        select(slug) {
            this.active = slug;
        },

        isActive(slug) {
            return this.active === slug;
        },
    };
}

/**
 * Scroll-spy for the sticky "In this guide" nav: the chapter crossing the
 * upper third of the viewport becomes the active link.
 */
export function guideToc(ids = []) {
    return {
        active: ids[0] ?? null,
        observer: null,

        init() {
            const chapters = ids.map((id) => document.getElementById(id)).filter(Boolean);
            if (!chapters.length || !('IntersectionObserver' in window)) return;

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) this.active = entry.target.id;
                    // Back above the first chapter (e.g. scrolled to the top): it is the current one again.
                    else if (entry.target === chapters[0] && entry.boundingClientRect.top > 0) this.active = chapters[0].id;
                });
            }, { rootMargin: '-25% 0px -65% 0px' });

            chapters.forEach((chapter) => this.observer.observe(chapter));
        },

        destroy() {
            this.observer?.disconnect();
        },
    };
}
