export const checkoutWizard = (initial = {}) => ({
    step: initial.step ?? 1,
    maxStep: 3,
    next() { if (this.step < this.maxStep) this.step++; },
    prev() { if (this.step > 1) this.step--; },
    goto(s) { if (s >= 1 && s <= this.maxStep) this.step = s; },
});
