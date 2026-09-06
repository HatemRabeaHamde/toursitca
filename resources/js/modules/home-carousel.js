export function homeCarousel({ autoplay = false, interval = 4200 } = {}) {
    return {
        autoplay,
        interval,
        timer: null,
        isPaused: false,

        init() {
            if (this.autoplay && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.start();
            }
        },

        start() {
            this.stop();

            this.timer = window.setInterval(() => {
                if (!this.isPaused) {
                    this.advance();
                }
            }, this.interval);
        },

        stop() {
            if (this.timer !== null) {
                window.clearInterval(this.timer);
                this.timer = null;
            }
        },

        pause() {
            this.isPaused = true;
        },

        resume() {
            this.isPaused = false;
        },

        previous() {
            this.pause();
            this.scrollByCard(-1);
        },

        next() {
            this.pause();
            this.scrollByCard(1);
        },

        advance() {
            const track = this.$refs.track;

            if (!track || track.scrollWidth <= track.clientWidth) {
                return;
            }

            const maxScroll = track.scrollWidth - track.clientWidth - 2;

            if (track.scrollLeft >= maxScroll) {
                track.scrollTo({ left: 0, behavior: 'auto' });
                return;
            }

            this.scrollByCard(1);
        },

        scrollByCard(direction) {
            const track = this.$refs.track;

            if (!track) {
                return;
            }

            const firstCard = track.firstElementChild;
            const gap = Number.parseFloat(window.getComputedStyle(track).columnGap || '0');
            const cardWidth = firstCard ? firstCard.getBoundingClientRect().width : track.clientWidth;

            track.scrollBy({
                left: direction * (cardWidth + gap),
                behavior: 'smooth',
            });
        },
    };
}
