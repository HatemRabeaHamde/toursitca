import { MOTION } from './motion-config.js';

/** Subtle depth on [data-parallax="<yPercent range>"] — desktop only. */
export function registerParallax({ gsap, root }) {
    const mm = gsap.matchMedia();

    mm.add(MOTION.parallaxMedia, () => {
        root.querySelectorAll('[data-parallax]').forEach((el) => {
            const range = Number.parseFloat(el.dataset.parallax) || 10;

            gsap.fromTo(el, { yPercent: -range / 2 }, {
                yPercent: range / 2,
                ease: 'none',
                scrollTrigger: { trigger: el.closest('section') ?? el, start: 'top bottom', end: 'bottom top', scrub: true },
            });
        });
    });

    return mm;
}
