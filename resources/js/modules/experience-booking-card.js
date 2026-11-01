/**
 * Experience detail v2 · booking card (Figma 09 "Booking card").
 *
 * A thin view layer over the existing `experienceBooking` store: the store
 * keeps doing quote + checkout exactly as before; this component only formats
 * values for the card (money, date range, travellers, total) and decides when
 * to refresh the quote.
 *
 * config = { store: {...store config}, card: { locale, currency, tripDays, strings }, options: [...] }
 */
export function experienceBookingCard(config) {
    const card = config.card;
    const strings = card.strings;
    const plural = (forms, count) => (count === 1 ? forms.one : forms.other).replace(':count', count);

    return {
        dateOpen: false,
        guestsOpen: false,
        formatter: null,

        init() {
            this.formatter = { whole: this.makeFormatter(0), cents: this.makeFormatter(2) };
            this.store.configure(config.store);

            (config.options || []).forEach((option) => this.store.registerOptionPricing(option.id, option));

            if (config.options?.length && this.store.activeOption === null) {
                this.store.activeOption = config.options[0].id;
            }
        },

        get store() {
            return this.$store.experienceBooking;
        },

        makeFormatter(digits) {
            try {
                return new Intl.NumberFormat(card.locale, {
                    style: 'currency',
                    currency: card.currency,
                    minimumFractionDigits: digits,
                    maximumFractionDigits: digits,
                });
            } catch {
                return { format: (value) => `${card.currency} ${Number(value).toFixed(digits)}` };
            }
        },

        money(value) {
            const amount = Number(value) || 0;

            return (Number.isInteger(amount) ? this.formatter.whole : this.formatter.cents).format(amount);
        },

        /* ── Labels ─────────────────────────────────────────── */

        unitPrice() {
            const store = this.store;

            if (store.bookingType === 'private' && store.config.privatePrice !== null) return store.config.privatePrice;

            const pricing = store.activePricing();
            if (pricing) return pricing.priceType === 'per_group' ? pricing.groupPrice : pricing.adultPrice;

            return store.config.pricePerPerson;
        },

        isFlat() {
            return (this.store.bookingType === 'private' && this.store.config.privatePrice !== null)
                || this.store.activePricing()?.priceType === 'per_group';
        },

        travellersLabel() {
            const parts = [plural(strings.adults, this.store.adults)];
            if (this.store.children > 0) parts.push(plural(strings.children, this.store.children));

            return parts.join(', ');
        },

        dateLabel() {
            const start = this.store.selectedDate;
            if (!start) return null;

            const from = new Date(`${start}T12:00:00`);
            const format = new Intl.DateTimeFormat(card.locale, { month: 'short', day: 'numeric', year: 'numeric' });

            if (card.tripDays <= 1 || typeof format.formatRange !== 'function') return format.format(from);

            const to = new Date(from);
            to.setDate(to.getDate() + card.tripDays - 1);

            return format.formatRange(from, to);
        },

        total() {
            return this.store.quote ? Number(this.store.quote.total) : this.store.liveTotal();
        },

        breakdown() {
            if (this.isFlat()) return strings.flat;

            return strings.breakdown
                .replace(':price', this.money(this.unitPrice()))
                .replace(':count', plural(strings.travellers, this.store.participantCount()));
        },

        /* ── Actions ────────────────────────────────────────── */

        openDates() {
            this.guestsOpen = false;
            this.dateOpen = true;
        },

        async pickDate(date, label, availabilityIds) {
            this.store.selectDate(date, label, availabilityIds);
            this.dateOpen = false;
            await this.refreshQuote();
        },

        canAdd() {
            return this.store.participantCount() < (this.store.config.maxParticipants || Infinity);
        },

        changeAdults(delta) {
            const next = this.store.adults + delta;
            if (next < 1 || (delta > 0 && !this.canAdd())) return;

            this.store.adults = next;
            this.store.clearQuote();
        },

        stepTravellers(delta) {
            this.changeAdults(delta);
            this.refreshQuote();
        },

        changeChildren(delta) {
            const next = this.store.children + delta;
            if (next < 0 || (delta > 0 && !this.canAdd())) return;

            this.store.children = next;
            this.store.syncChildAges();
            this.store.clearQuote();
        },

        setChildAge(index, value) {
            this.store.childAges[index] = value === '' ? null : parseInt(value, 10);
            this.store.clearQuote();
        },

        closeGuests() {
            if (!this.guestsOpen) return;

            this.guestsOpen = false;
            this.refreshQuote();
        },

        setBookingType(type) {
            this.store.setBookingType(type);
            this.refreshQuote();
        },

        setOption(id) {
            const optionId = id === '' ? null : Number(id);
            if (this.store.activeOption !== optionId) this.store.setActiveOption(optionId);
            if (!this.store.quote) this.refreshQuote();
        },

        setLanguage(code) {
            this.store.selectedLanguage = code;
            this.store.clearQuote();
            this.refreshQuote();
        },

        async refreshQuote() {
            if (!this.store.hasSelection(this.store.activeOption)) return null;

            return this.store.applyQuote();
        },

        request() {
            if (!this.store.selectedDate) {
                this.openDates();
                return;
            }

            this.store.startCheckout(this.store.activeOption);
        },
    };
}
