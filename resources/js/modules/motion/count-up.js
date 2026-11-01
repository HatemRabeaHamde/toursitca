import { MOTION } from './motion-config.js';

/** Counts a [data-count] element from 0 to its value in the page locale. */
export function countUp(gsap, el) {
    const raw = String(el.dataset.count ?? '0').replace(/,/g, '');
    const target = Number.parseFloat(raw) || 0;
    const decimals = (raw.split('.')[1] ?? '').length;
    const format = new Intl.NumberFormat(document.documentElement.lang || 'en', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
    const state = { value: 0 };

    return gsap.timeline()
        .fromTo(el, { y: 18, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: MOTION.dur.base, ease: MOTION.ease.soft })
        .to(state, {
            value: target,
            duration: 2,
            ease: 'power2.out',
            onUpdate: () => { el.textContent = format.format(state.value); },
        }, 0);
}
