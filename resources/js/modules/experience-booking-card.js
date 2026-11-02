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
        options: config.options || [],
        dateOpen: false,
        optionsOpen: false,
        sheetOpen: false,
        calendarMonth: 0,
        guestsOpen: false,
        formatter: null,

        init() {
            this.formatter = { whole: this.makeFormatter(0), cents: this.makeFormatter(2) };
            this.store.configure(config.store);

            (config.options || []).forEach((option) => this.store.registerOptionPricing(option.id, option));

            // Links to the booking form (#book) open the sheet on phones, where the form is not in the page flow.
            const openFromHash = () => { if (window.location.hash === '#book' && window.matchMedia('(max-width: 1023px)').matches) this.sheetOpen = true; };
            openFromHash();
            window.addEventListener('hashchange', openFromHash);

            // Coming back from checkout (back button / bfcache) must not leave the form stuck on "One moment…".
            window.addEventListener('pageshow', (event) => { if (event.persisted) this.store.isStartingCheckout = false; });

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

            const pricing = store.activePricing();
            if (pricing?.priceType === 'per_group') return pricing.groupPrice;
            if (store.bookingType === 'private' && store.config.privatePrice !== null) return store.config.privatePrice;
            if (pricing) return pricing.adultPrice;

            return store.config.pricePerPerson;
        },

        isFlat() {
            return (this.store.bookingType === 'private' && this.store.config.privatePrice !== null)
                || this.store.activePricing()?.priceType === 'per_group';
        },

        /** Packages are set by the host: a per-group price says nothing about shared or private, so the toggle just hides. */
        isGroupPriced() {
            return this.store.activePricing()?.priceType === 'per_group';
        },

        isPrivateTrip() {
            return this.store.bookingType === 'private';
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
            const option = this.chosenOption();
            if (option) return this.optionBreakdown(option);

            return strings.breakdown.replace(':price', this.money(this.unitPrice()))
                .replace(':count', plural(strings.travellers, this.store.participantCount()));
        },

        /* ── Actions ────────────────────────────────────────── */

        openDates() {
            this.guestsOpen = false;
            this.dateOpen = true;
        },

        async pickDate(date, label, availabilityIds, slots = []) {
            if (this.store.isStartingCheckout) return;
            this.store.selectDate(date, label, availabilityIds);
            this.store.selectedDaySlots = slots;
            this.store.selectedTime = null;
            if (slots.length) {
                const times = this.timeChoices();
                if (times.length === 1) this.store.selectedTime = times[0];
                this.syncTimeAvailability();
            }
            this.dateOpen = false;
            await this.syncOptionToDate();
            if (!this.store.quote) await this.refreshQuote();
            this.revealOptions();
        },

        timeChoices() {
            return [...new Set(this.store.selectedDaySlots.filter(slot => slot.seats >= this.store.participantCount()
                && (this.store.bookingType !== 'private' || slot.privateOpen)).map(slot => slot.time))].sort();
        },

        syncTimeAvailability() {
            if (!this.store.selectedDaySlots.length) return;
            const ids = {};
            const slots = this.store.selectedDaySlots.filter(slot => slot.time === this.store.selectedTime
                && slot.seats >= this.store.participantCount() && (this.store.bookingType !== 'private' || slot.privateOpen));
            for (const slot of slots) ids[slot.optionId ?? 'default'] = slot.id;
            if (slots.length) ids.any = slots[0].id;
            this.store.selectedAvailabilityIds = ids;
        },

        async selectTime(time) {
            if (this.store.isStartingCheckout) return;
            this.store.selectedTime = time;
            this.store.clearQuote();
            this.syncTimeAvailability();
            await this.syncOptionToDate();
            if (!this.store.quote) await this.refreshQuote();
        },

        async refreshSelection() {
            if (this.store.selectedDaySlots.length) {
                if (!this.timeChoices().includes(this.store.selectedTime)) this.store.selectedTime = null;
                this.syncTimeAvailability();
            }
            await this.syncOptionToDate();
            if (!this.store.quote) await this.refreshQuote();
        },

        /* ── Options (packages) ─────────────────────────────── */

        optionAvailable(option) {
            return !this.store.config.preview && Boolean(this.store.selectedDate) && this.store.availabilityForOption(option.id) !== null;
        },

        availableOptions() {
            return this.options.filter((option) => this.optionAvailable(option));
        },

        chosenOption() {
            return this.options.find((option) => option.id === this.store.activeOption) || null;
        },

        needsOption() {
            return this.options.length > 0 && this.chosenOption() === null;
        },

        /** Per-person or per-group price of an option for the current travellers (before the server quote). */
        optionTotal(option, original = false) {
            const pick = (live, base) => (original ? base : live);
            const quote = !original ? this.store.quoteForOption(option.id) : null;
            if (quote) return Number(quote.total);

            if (option.priceType === 'per_group') return pick(option.groupPrice, option.groupOriginal) || 0;

            if (!original && this.store.bookingType === 'private' && this.store.config.privatePrice !== null) {
                return this.store.config.privatePrice;
            }

            return (this.store.adults * (pick(option.adultPrice, option.adultOriginal) || 0))
                + (this.store.children * (pick(option.childPrice, option.childOriginal) || 0));
        },

        optionDiscount(option) {
            const was = this.optionTotal(option, true);
            const now = this.optionTotal(option);

            return was > now && was > 0 ? Math.round((1 - now / was) * 100) : null;
        },

        optionBreakdown(option) {
            if (option.priceType === 'per_group' || this.store.bookingType === 'private') return strings.flat;

            const parts = [this.money(option.adultPrice) + ' × ' + plural(strings.adults, this.store.adults)];
            if (this.store.children) parts.push(this.money(option.childPrice) + ' × ' + plural(strings.children, this.store.children));
            return parts.join(' + ');
        },

        async selectOption(option) {
            if (!this.optionAvailable(option)) return;

            this.store.clearQuote();
            if (!option.private && this.store.bookingType === 'private') this.store.setBookingType('group');
            this.store.activeOption = option.id;
            this.store.quote = this.store.quotesByOption[String(option.id)] || null;
            this.store.quoteError = null;

            const codes = option.languages.map((language) => language.code);
            if (codes.length && !codes.includes(this.store.selectedLanguage)) this.store.selectedLanguage = codes[0];

            await this.refreshQuote();
        },

        /** After a new date: drop an option that is not open that day; a single open option is chosen for the traveller. */
        async syncOptionToDate() {
            const chosen = this.chosenOption();
            if (chosen && !this.optionAvailable(chosen)) {
                this.store.activeOption = null;
                return;
            }

            const open = this.availableOptions();
            if (this.store.activeOption === null && open.length === 1) await this.selectOption(open[0]);
        },

        /** After a date: the options section on wide screens, the options dialog on phones. */
        revealOptions() {
            if (this.options.length === 0) return;

            if (window.matchMedia('(min-width: 1024px)').matches) {
                this.$nextTick(() => document.getElementById('options')?.scrollIntoView({ behavior: 'smooth', block: 'start' }));
                return;
            }

            if (this.options.length > 1 && !this.chosenOption()) this.optionsOpen = true;
        },

        openOptions() {
            if (!this.store.selectedDate) return this.openDates();

            this.optionsOpen = true;
        },

        async chooseOption(option) {
            await this.selectOption(option);
            if (this.chosenOption()?.id === option.id) this.optionsOpen = false;
        },

        /** Escape closes the top layer only: a dialog first, then the phone sheet. */
        closeLayers() {
            if (this.dateOpen || this.optionsOpen) {
                this.dateOpen = false;
                this.optionsOpen = false;
                return;
            }

            this.closeGuests();
            this.sheetOpen = false;
        },

        languageChoices() {
            return this.chosenOption()?.languages || [];
        },

        /** The request button stays inactive until a date is picked and an option is chosen (when the trip has options). */
        canRequest() {
            return Boolean(this.store.selectedDate)
                && !this.needsOption()
                && this.store.hasSelection(this.store.activeOption)
                && !this.store.isStartingCheckout
                && !this.store.isQuoting
                && !this.store.quoteError
                && this.store.quote !== null
                && this.store.childAges.every(age => Number.isInteger(age) && this.childAgeAllowed(age));
        },

        /** What is still missing, in plain words; null when ready. */
        nextStepHint() {
            if (!this.store.selectedDate) return strings.hintDate;
            if (this.store.selectedDaySlots.length && !this.store.selectedTime) return strings.hintTime;
            if (this.store.childAges.some(age => !Number.isInteger(age))) return strings.hintAges;
            if (this.needsOption()) return strings.hintOption;

            return null;
        },

        proceed() {
            if (this.canRequest()) this.store.startCheckout(this.store.activeOption);
        },

        /** A child's age must sit inside the chosen package's child band (0–17 when the host set none). */
        childAgeAllowed(age) {
            const option = this.chosenOption();

            return age >= (option?.childMinAge ?? 0) && age <= (option?.childMaxAge ?? 17);
        },

        canAdd() {
            return this.store.participantCount() < (this.store.config.maxParticipants || Infinity);
        },

        changeAdults(delta) {
            if (this.store.isStartingCheckout) return;
            const next = this.store.adults + delta;
            if (next < 1 || (delta > 0 && !this.canAdd())) return;

            this.store.adults = next;
            this.store.clearQuote();
        },

        stepTravellers(delta) {
            this.changeAdults(delta);
            this.refreshSelection();
        },

        changeChildren(delta) {
            if (this.store.isStartingCheckout) return;
            const next = this.store.children + delta;
            if (next < 0 || (delta > 0 && !this.canAdd())) return;

            this.store.children = next;
            this.store.syncChildAges();
            this.store.clearQuote();
        },

        setChildAge(index, value) {
            if (this.store.isStartingCheckout) return;
            this.store.childAges[index] = value === '' ? null : parseInt(value, 10);
            this.store.clearQuote();
        },

        closeGuests() {
            if (!this.guestsOpen) return;

            this.guestsOpen = false;
            this.refreshSelection();
        },

        setBookingType(type) {
            if (type === 'private' && this.chosenOption() && !this.chosenOption().private) return;
            this.store.setBookingType(type);
            this.refreshSelection();
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
            if (this.store.config.preview || this.needsOption() || !this.store.hasSelection(this.store.activeOption)) return null;

            return this.store.applyQuote();
        },

        request() {
            if (!this.store.selectedDate) {
                this.openDates();
                return;
            }

            if (this.needsOption()) {
                this.store.quoteError = strings.chooseOption;
                document.getElementById('options')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }

            if (this.canRequest()) this.store.startCheckout(this.store.activeOption);
        },
    };
}
