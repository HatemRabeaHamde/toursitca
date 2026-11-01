/**
 * Motion tokens for landing v2 — the "tempo" every animation follows.
 * One beat = the gap between two notes (items) in a sequence.
 */
export const MOTION = {
    beat: 0.12,
    cue: '<0.14', // default position: 0.14s after the previous item starts
    start: 'top 80%',
    dur: { short: 0.6, base: 0.9, long: 1.2, slow: 1.6 },
    ease: {
        out: 'expo.out',
        soft: 'power3.out',
        inOut: 'power3.inOut',
        pop: 'back.out(1.6)',
    },
    parallaxMedia: '(min-width: 768px)',
    // Header clearance comes from CSS scroll-margin-top on anchored sections (Lenis honours it).
    anchorOffset: 0,
};
