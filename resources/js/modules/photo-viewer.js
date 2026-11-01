/**
 * Full-screen photo viewer (Experience detail v2 · "View all N photos").
 * Keyboard: ←/→ to browse, Esc to close. Focus is trapped by x-trap in the markup.
 */
export function photoViewer(photos = []) {
    return {
        open: false,
        current: 0,
        photos,

        get photo() {
            return this.photos[this.current] ?? null;
        },

        show(index = 0) {
            this.current = index;
            this.open = true;
        },

        close() {
            this.open = false;
        },

        next() {
            this.current = (this.current + 1) % this.photos.length;
        },

        prev() {
            this.current = (this.current - 1 + this.photos.length) % this.photos.length;
        },
    };
}
