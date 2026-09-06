export function registerExperienceBookingStore(Alpine) {
    Alpine.store('experienceBooking', {
        adults: 1,
        children: 0,
        childAges: [],
        bookingType: 'group',
        selectedDate: null,
        selectedDateLabel: null,
        selectedAvailabilityIds: {},
        selectedLanguage: null,
        activeOption: null,
        availabilityChecked: false,
        quote: null,
        quotesByOption: {},
        quoteError: null,
        isQuoting: false,
        isStartingCheckout: false,
        config: {
            checkoutUrl: null,
            csrfToken: null,
            experienceId: null,
            maxParticipants: null,
            privateAvailable: false,
            quoteUrl: null,
            pricePerPerson: 0,
            privatePrice: null,
            currency: 'MAD',
            strings: {},
        },

        configure(config) {
            this.config = {
                ...this.config,
                ...config,
                strings: {
                    ...this.config.strings,
                    ...(config.strings || {}),
                },
            };

            if (! this.selectedLanguage && config.defaultLanguage) {
                this.selectedLanguage = config.defaultLanguage;
            }
        },

        participantCount() {
            return this.adults + this.children;
        },

        participants() {
            return {
                adult: this.adults,
                ...(this.children > 0 ? { child: this.children } : {}),
            };
        },

        syncChildAges() {
            const current = this.childAges.length;
            if (this.children > current) {
                for (let i = current; i < this.children; i++) {
                    this.childAges.push(null);
                }
            } else if (this.children < current) {
                this.childAges = this.childAges.slice(0, this.children);
            }
        },

        setBookingType(type) {
            if (type === 'private' && ! this.config.privateAvailable) {
                return;
            }

            this.bookingType = type;
            this.clearQuote();
        },

        selectDate(date, label, availabilityIds = {}) {
            this.selectedDate = date;
            this.selectedDateLabel = label;
            this.selectedAvailabilityIds = availabilityIds || {};
            this.availabilityChecked = false;
            this.clearQuote();
        },

        clearQuote() {
            this.quote = null;
            this.quotesByOption = {};
            this.quoteError = null;
        },

        setActiveOption(optionId) {
            const nextOption = this.activeOption === optionId ? null : optionId;
            const key = nextOption === null || nextOption === undefined ? 'default' : String(nextOption);

            this.activeOption = nextOption;
            this.quote = this.quotesByOption[key] || null;
            this.quoteError = null;
        },

        availabilityForOption(optionId = null) {
            const ids = this.selectedAvailabilityIds || {};
            const optionKey = optionId === null || optionId === undefined ? null : String(optionId);

            return (optionKey && ids[optionKey]) || ids.default || ids.any || null;
        },

        hasSelection(optionId = null) {
            return Boolean(this.selectedDate && this.availabilityForOption(optionId) && this.participantCount() > 0);
        },

        quoteForOption(optionId = null) {
            const key = optionId === null || optionId === undefined ? 'default' : String(optionId);

            return this.quotesByOption[key] || (this.activeOption === optionId ? this.quote : null);
        },

        requestPayload(optionId = null) {
            const payload = {
                experience_id: this.config.experienceId,
                option_id: optionId,
                availability_id: this.availabilityForOption(optionId),
                booking_type: this.bookingType,
                participants: this.participants(),
                language: this.selectedLanguage,
                payment_method: 'manual',
            };

            if (this.children > 0 && this.childAges.length > 0) {
                payload.child_ages = this.childAges.map(age => age ?? 0);
            }

            return payload;
        },

        async applyQuote(optionId = null) {
            const targetOption = optionId ?? this.activeOption;

            if (! this.hasSelection(targetOption)) {
                this.quoteError = this.config.strings.selectDateAndParticipants || 'Select date and participants first.';
                return null;
            }

            this.isQuoting = true;
            this.quoteError = null;

            try {
                const response = await fetch(this.config.quoteUrl, {
                    method: 'POST',
                    headers: this.headers(),
                    body: JSON.stringify(this.requestPayload(targetOption)),
                });
                const payload = await response.json();

                if (! response.ok) {
                    throw new Error(this.errorMessage(payload));
                }

                const quote = payload.data;
                const key = targetOption === null || targetOption === undefined ? 'default' : String(targetOption);

                this.quote = quote;
                this.availabilityChecked = true;
                this.quotesByOption = {
                    ...this.quotesByOption,
                    [key]: quote,
                };
                this.activeOption = targetOption;

                return quote;
            } catch (error) {
                this.quoteError = error.message || this.config.strings.quoteFailed || 'Could not check availability.';
                return null;
            } finally {
                this.isQuoting = false;
            }
        },

        async startCheckout(optionId = null) {
            const targetOption = optionId ?? this.activeOption;

            if (! this.hasSelection(targetOption)) {
                this.quoteError = this.config.strings.selectDateAndParticipants || 'Select date and participants first.';
                return;
            }

            this.isStartingCheckout = true;
            this.quoteError = null;

            try {
                const response = await fetch(this.config.checkoutUrl, {
                    method: 'POST',
                    headers: this.headers(),
                    body: JSON.stringify(this.requestPayload(targetOption)),
                });
                const payload = await response.json();

                if (! response.ok) {
                    throw new Error(this.errorMessage(payload));
                }

                window.location.href = payload.data.activity_url;
            } catch (error) {
                this.quoteError = error.message || this.config.strings.checkoutFailed || 'Could not start checkout.';
                this.isStartingCheckout = false;
            }
        },

        headers() {
            return {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '',
            };
        },

        errorMessage(payload) {
            const firstError = payload?.errors ? Object.values(payload.errors)[0]?.[0] : null;

            return firstError || payload?.message || this.config.strings.quoteFailed || 'Could not check availability.';
        },

        optionPricing: {},

        registerOptionPricing(optionId, pricing) {
            this.optionPricing = { ...this.optionPricing, [String(optionId)]: pricing };
        },

        activePricing() {
            if (this.activeOption === null || this.activeOption === undefined) return null;
            return this.optionPricing[String(this.activeOption)] || null;
        },

        activeOptionTitle() {
            return this.activePricing()?.title || null;
        },

        liveTotal() {
            if (this.bookingType === 'private' && this.config.privatePrice !== null) {
                return this.config.privatePrice;
            }

            const p = this.activePricing();

            if (p) {
                if (p.priceType === 'per_group') return Number(p.groupPrice) || 0;
                return (this.adults * (Number(p.adultPrice) || 0)) + (this.children * (Number(p.childPrice) || 0));
            }

            return this.config.pricePerPerson * (this.adults + this.children);
        },

        liveTotalFormatted() {
            return this.config.currency + ' ' + this.liveTotal().toFixed(2);
        },

        liveBreakdown() {
            const cur = this.config.currency;
            const s = this.config.strings;

            if (this.bookingType === 'private' && this.config.privatePrice !== null) {
                return s.privateFlat || 'Private — flat rate';
            }

            const p = this.activePricing();

            if (p) {
                if (p.priceType === 'per_group') {
                    return '1 × ' + (s.group || 'Group') + ' · ' + cur + ' ' + (Number(p.groupPrice) || 0).toFixed(2);
                }
                const parts = [];
                if (this.adults > 0) parts.push(this.adults + ' × ' + (s.adult || 'Adult') + ' · ' + cur + ' ' + (Number(p.adultPrice) || 0).toFixed(2));
                if (this.children > 0) parts.push(this.children + ' × ' + (s.child || 'Child') + ' · ' + cur + ' ' + (Number(p.childPrice) || 0).toFixed(2));
                return parts.join('   +   ');
            }

            return (this.adults + this.children) + ' × ' + cur + ' ' + this.config.pricePerPerson.toFixed(2);
        },

        livePriceLabel() {
            if (this.bookingType === 'private' && this.config.privatePrice !== null) {
                return this.config.currency + ' ' + this.config.privatePrice.toFixed(2);
            }

            const p = this.activePricing();

            if (p) {
                const unit = p.priceType === 'per_group' ? p.groupPrice : p.adultPrice;
                return this.config.currency + ' ' + (Number(unit) || 0).toFixed(2);
            }

            return this.config.currency + ' ' + this.config.pricePerPerson.toFixed(2);
        },

        livePriceSuffix() {
            const s = this.config.strings;
            if (this.bookingType === 'private' && this.config.privatePrice !== null) return s.flatRate || 'Flat rate';
            if (this.activePricing()?.priceType === 'per_group') return s.perGroup || 'Per group';
            return s.perPerson || 'Per person';
        },
    });
}
