import { MOTION } from './motion-config.js';
import { countUp } from './count-up.js';

/**
 * One recipe per [data-reveal] value. Each returns a tween/timeline that the
 * conductor (reveal.js) places on its section timeline.
 */
const childrenOf = (el) => {
    const marked = el.querySelectorAll(':scope [data-reveal-item]');
    return marked.length ? [...marked] : [...el.children];
};

// Hover transitions on these elements would fight GSAP's inline transforms.
const pauseTransition = (gsap, targets) => gsap.set(targets, { transition: 'none' });

export const RECIPES = {
    fade: (gsap, el) => gsap.fromTo(el,
        { y: 24, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: MOTION.dur.base, ease: MOTION.ease.soft, clearProps: 'transform' }),

    text: (gsap, el) => gsap.fromTo(el,
        { y: 34, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: MOTION.dur.long, ease: MOTION.ease.out, clearProps: 'transform' }),

    heading: (gsap, el, { SplitText }) => {
        const split = SplitText.create(el, { type: 'lines', mask: 'lines', linesClass: 'lv-line' });

        return gsap.timeline({ onComplete: () => split.revert() })
            .set(el, { autoAlpha: 1 })
            .from(split.lines, { yPercent: 115, duration: 1.3, stagger: MOTION.beat, ease: MOTION.ease.out });
    },

    image: (gsap, el) => {
        const img = el.tagName === 'IMG' ? el : el.querySelector('img');
        const tl = gsap.timeline();

        if (img) pauseTransition(gsap, img);
        tl.fromTo(el,
            { clipPath: 'inset(100% 0% 0% 0%)', autoAlpha: 1 },
            { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.4, ease: 'expo.inOut', clearProps: 'clipPath' });
        if (img && img !== el) {
            tl.fromTo(img, { scale: 1.25 }, { scale: 1, duration: 1.9, ease: MOTION.ease.out, clearProps: 'transform,transition' }, 0);
        }

        return tl;
    },

    rule: (gsap, el) => gsap.fromTo(el,
        { scaleX: 0, transformOrigin: '0% 50%', autoAlpha: 1 },
        { scaleX: 1, duration: MOTION.dur.long, ease: MOTION.ease.inOut, clearProps: 'transform' }),

    pop: (gsap, el) => gsap.fromTo(el,
        { scale: 0.7, autoAlpha: 0 },
        { scale: 1, autoAlpha: 1, duration: MOTION.dur.short, ease: MOTION.ease.pop, clearProps: 'transform' }),

    stagger: (gsap, el) => {
        const items = childrenOf(el);

        return gsap.timeline()
            .set(el, { autoAlpha: 1 })
            .fromTo(items,
                { y: 60, autoAlpha: 0 },
                { y: 0, autoAlpha: 1, duration: MOTION.dur.long, ease: MOTION.ease.out, stagger: MOTION.beat, clearProps: 'transform' });
    },

    // Alternating heights + slight tilt: cards arrive like a rolling wave.
    wave: (gsap, el) => {
        const items = childrenOf(el);

        return gsap.timeline()
            .set(el, { autoAlpha: 1 })
            .fromTo(items,
                { y: (i) => (i % 2 ? 120 : 70), rotation: (i) => (i % 2 ? 3 : -3), autoAlpha: 0 },
                { y: 0, rotation: 0, autoAlpha: 1, duration: 1.4, ease: MOTION.ease.out, stagger: 0.09, clearProps: 'transform' });
    },

    count: (gsap, el) => countUp(gsap, el),
};
