import Lenis from 'lenis';
import { MOTION } from './motion-config.js';

/**
 * Inertial smooth scrolling driven by GSAP's ticker so ScrollTrigger and
 * Lenis share one frame loop (no double RAF, no jitter).
 */
export function createSmoothScroll(gsap, ScrollTrigger) {
    const lenis = new Lenis({ lerp: 0.085, wheelMultiplier: 0.95, autoRaf: false });

    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    return lenis;
}

/** Same-page anchors (#discover, #plan…) glide instead of jumping. */
export function registerAnchors(lenis, root) {
    root.addEventListener('click', (event) => {
        const link = event.target.closest('a[href^="#"]');
        const hash = link?.getAttribute('href');

        if (!hash || hash.length < 2) return;

        const target = document.getElementById(decodeURIComponent(hash.slice(1)));
        if (!target) return;

        event.preventDefault();
        lenis.scrollTo(target, { offset: MOTION.anchorOffset, duration: 1.6 });
        history.replaceState(null, '', hash);
    });
}

/**
 * Arriving on /page#section: land exactly under the fixed header instead of
 * the browser's raw jump, and let other modules ask for a glide
 * (`lv:scroll-to`) or a layout refresh (`lv:layout`).
 */
export function registerScrollEvents(lenis, ScrollTrigger) {
    const hash = window.location.hash.slice(1);
    const target = hash ? document.getElementById(decodeURIComponent(hash)) : null;
    if (target) {
        const land = () => lenis.scrollTo(target, { offset: MOTION.anchorOffset, immediate: true, force: true });
        // Once now, and again when images/fonts have settled the layout.
        const settle = () => requestAnimationFrame(() => { ScrollTrigger.refresh(); land(); });
        land();
        if (document.readyState === 'complete') settle();
        else window.addEventListener('load', settle, { once: true });
    }

    window.addEventListener('lv:scroll-to', (event) => {
        const goal = event.detail?.target;
        if (!goal) return;
        if (goal.getBoundingClientRect().top < 0) {
            lenis.scrollTo(goal, { offset: MOTION.anchorOffset, duration: 1.1 });
        }
    });

    window.addEventListener('lv:layout', () => ScrollTrigger.refresh());
}
