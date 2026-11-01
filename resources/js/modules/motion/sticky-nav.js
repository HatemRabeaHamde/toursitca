/**
 * Header states: transparent over the hero, frosted once scrolled, tucked
 * away while scrolling down and back on the first scroll up.
 */
export function registerStickyNav(lenis, header) {
    if (!header) return;

    const SCROLLED_AT = 40;
    let lastY = 0;

    const update = (y) => {
        const menuOpen = header.querySelector('[aria-expanded="true"]') !== null;
        const goingDown = y > lastY + 2;
        const goingUp = y < lastY - 2;

        header.classList.toggle('is-scrolled', y > SCROLLED_AT);
        if (goingDown && y > window.innerHeight * 0.6 && !menuOpen) header.classList.add('is-hidden');
        if (goingUp || y <= SCROLLED_AT) header.classList.remove('is-hidden');

        lastY = y;
    };

    lenis.on('scroll', ({ scroll }) => update(scroll));
    header.addEventListener('focusin', () => header.classList.remove('is-hidden'));
    update(window.scrollY);
}
