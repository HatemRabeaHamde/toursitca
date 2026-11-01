import { MOTION } from './motion-config.js';

const INTRO_SEEN_KEY = 'lv-intro-seen';

/**
 * The opening: image → nav → gold rule → eyebrow → title line by line →
 * copy → actions → cities. Then a scrubbed exit as the hero scrolls away.
 *
 * The full overture plays once per visit. On the next pages the header is
 * already in place (html.lv-returning, set before first paint by the layout)
 * and the hero plays a shorter, quicker version — moving between pages
 * feels like turning a page, not reloading the site.
 */
export function playHeroIntro({ gsap, SplitText, root }) {
    const hero = root.querySelector('[data-hero]');

    // Pages without a cinematic hero still need the nav (and any intro element) visible.
    if (!hero) {
        gsap.set(root.querySelectorAll('[data-hero-intro]'), { autoAlpha: 1 });
        return null;
    }

    const returning = document.documentElement.classList.contains('lv-returning');
    const q = (selector) => root.querySelectorAll(selector);
    const title = hero.querySelector('[data-hero-title]');
    const split = SplitText.create(title, { type: 'lines', mask: 'lines', linesClass: 'lv-line' });

    const tl = gsap.timeline({
        defaults: { ease: MOTION.ease.out, duration: MOTION.dur.long },
        onComplete: () => split.revert(),
    });

    if (returning) {
        gsap.set(q('[data-hero-nav]'), { autoAlpha: 1 });
        tl.fromTo(q('[data-hero-media]'), { scale: 1.06, autoAlpha: 0 }, { scale: 1, autoAlpha: 1, duration: 1.4, ease: 'power2.out' }, 0)
            .fromTo(q('[data-hero-rule]'), { scaleX: 0, autoAlpha: 1 }, { scaleX: 1, duration: MOTION.dur.short, ease: MOTION.ease.inOut }, 0.1)
            .fromTo(q('[data-hero-eyebrow]'), { x: -10, autoAlpha: 0 }, { x: 0, autoAlpha: 1, duration: MOTION.dur.short }, 0.15)
            .set(title, { autoAlpha: 1 }, 0.2)
            .from(split.lines, { yPercent: 110, duration: 1, stagger: 0.08 }, 0.2)
            .fromTo(q('[data-hero-fade]'), { y: 18, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: MOTION.dur.short, stagger: 0.06, clearProps: 'transform' }, 0.5)
            .fromTo(q('[data-hero-city]'), { y: 14, autoAlpha: 0 }, { y: 0, autoAlpha: 1, stagger: 0.05, duration: MOTION.dur.short, clearProps: 'transform' }, 0.65);

        registerHeroExit({ gsap, hero });
        return tl;
    }

    rememberIntro();

    tl.fromTo(q('[data-hero-media]'), { scale: 1.16, autoAlpha: 0 }, { scale: 1, autoAlpha: 1, duration: 2.6, ease: 'power2.out' }, 0)
        .fromTo(q('[data-hero-nav]'), { y: -28, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: MOTION.dur.base, clearProps: 'transform' }, 0.35)
        .fromTo(q('[data-hero-rule]'), { scaleX: 0, autoAlpha: 1 }, { scaleX: 1, duration: MOTION.dur.base, ease: MOTION.ease.inOut }, 0.55)
        .fromTo(q('[data-hero-eyebrow]'), { x: -14, autoAlpha: 0 }, { x: 0, autoAlpha: 1, duration: MOTION.dur.base }, 0.85)
        .set(title, { autoAlpha: 1 }, 0.9)
        .from(split.lines, { yPercent: 115, duration: 1.4, stagger: 0.14 }, 0.9)
        .fromTo(q('[data-hero-fade]'), { y: 30, autoAlpha: 0 }, { y: 0, autoAlpha: 1, stagger: MOTION.beat, clearProps: 'transform' }, 1.45)
        .fromTo(q('[data-hero-city]'), { y: 22, autoAlpha: 0 }, { y: 0, autoAlpha: 1, stagger: 0.08, duration: MOTION.dur.base, clearProps: 'transform' }, 1.8);

    registerHeroExit({ gsap, hero });

    return tl;
}

function rememberIntro() {
    try {
        sessionStorage.setItem(INTRO_SEEN_KEY, '1');
    } catch {
        // Private mode / blocked storage: the full intro simply plays again.
    }
}

/** As the visitor scrolls out: background drifts, copy lifts and softens. */
function registerHeroExit({ gsap, hero }) {
    const media = hero.querySelector('[data-hero-media]');
    const content = hero.querySelector('[data-hero-content]');
    const scrollTrigger = { trigger: hero, start: 'top top', end: 'bottom top', scrub: true };

    if (media) gsap.to(media, { yPercent: 18, ease: 'none', scrollTrigger });
    if (content) gsap.to(content, { yPercent: -12, opacity: 0.25, ease: 'none', scrollTrigger });
}
