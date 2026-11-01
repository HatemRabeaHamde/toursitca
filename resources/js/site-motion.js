/**
 * Tourstica v2 motion entry — loaded by layouts/site-v2 on every new public page.
 * Order matters: smooth scroll → hero intro → section reveals → parallax → nav.
 */
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';
import 'lenis/dist/lenis.css';

import { createSmoothScroll, registerAnchors, registerScrollEvents } from './modules/motion/smooth-scroll.js';
import { playHeroIntro } from './modules/motion/hero-intro.js';
import { registerReveals } from './modules/motion/reveal.js';
import { registerParallax } from './modules/motion/parallax.js';
import { registerStickyNav } from './modules/motion/sticky-nav.js';

const html = document.documentElement;
const FONT_WAIT_MS = 800;

function markReady() {
    html.classList.add('lv-motion-ready');
}

async function boot() {
    const root = document.querySelector('[data-lv-motion]');
    if (!root) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        markReady();
        return;
    }

    gsap.registerPlugin(ScrollTrigger, SplitText);

    // Split text only after the real fonts are in, so line breaks are final.
    await Promise.race([document.fonts.ready, new Promise((resolve) => setTimeout(resolve, FONT_WAIT_MS))]);

    const lenis = createSmoothScroll(gsap, ScrollTrigger);
    const context = { gsap, ScrollTrigger, SplitText, root };

    registerAnchors(lenis, root);
    playHeroIntro(context);
    registerReveals(context);
    registerParallax(context);
    registerStickyNav(lenis, root.querySelector('[data-lv-header]'));
    registerScrollEvents(lenis, ScrollTrigger);
    markReady();

    window.addEventListener('load', () => ScrollTrigger.refresh(), { once: true });
}

boot().catch(() => html.classList.add('lv-motion-failed'));
