export const mediaGallery = () => ({
    current: 0,
    lightbox: false,
    select(i) { this.current = i; },
    next() { this.current = (this.current + 1) % this.$refs.items.children.length; },
    prev() {
        const n = this.$refs.items.children.length;
        this.current = (this.current - 1 + n) % n;
    },
});
