export const availabilityPicker = (config = {}) => ({
    experienceId: config.experienceId,
    unitPrice: config.unitPrice ?? 0,
    selectedDate: null,
    selectedSlotId: null,
    participants: 1,
    slots: [],
    loading: false,
    error: null,

    get total() { return this.unitPrice * this.participants; },
    get canBook() { return this.selectedSlotId && this.participants > 0; },

    async pickDate(date) {
        this.selectedDate = date;
        this.selectedSlotId = null;
        this.loading = true;
        this.error = null;
        try {
            const res = await fetch(`/experiences/${this.experienceId}/slots?date=${date}`);
            this.slots = await res.json();
        } catch (e) {
            this.error = 'Could not load slots';
        } finally {
            this.loading = false;
        }
    },

    pickSlot(id) { this.selectedSlotId = id; },

    inc() { if (this.participants < 20) this.participants++; },
    dec() { if (this.participants > 1) this.participants--; },
});
