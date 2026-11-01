import { MOTION } from './motion-config.js';
import { RECIPES } from './reveal-recipes.js';

/**
 * The conductor. Every [data-reveal-group] becomes one timeline that plays
 * once when it enters the viewport; its [data-reveal] items play in DOM
 * order, each cued `data-reveal-at` (default MOTION.cue) after the previous.
 * A [data-reveal] with no group is its own one-note group.
 */
export function registerReveals({ gsap, ScrollTrigger, SplitText, root }) {
    const groups = new Map();

    root.querySelectorAll('[data-reveal]').forEach((item) => {
        const group = item.closest('[data-reveal-group]') ?? item;
        if (!groups.has(group)) groups.set(group, []);
        groups.get(group).push(item);
    });

    groups.forEach((items, group) => {
        const timeline = gsap.timeline({ paused: true });

        items.forEach((item, index) => {
            const recipe = RECIPES[item.dataset.reveal] ?? RECIPES.fade;
            const position = index === 0 ? 0 : (item.dataset.revealAt ?? MOTION.cue);
            timeline.add(recipe(gsap, item, { SplitText }), position);
        });

        ScrollTrigger.create({
            trigger: group,
            start: group.dataset.revealStart ?? MOTION.start,
            once: true,
            onEnter: () => timeline.play(),
        });
    });
}
