export function registerExperienceBookingStore(Alpine) {
    Alpine.store('experienceBooking', {
        adults: 1,
        children: 0,
        rangeStart: null,
        rangeEnd: null,
        rangeStartLabel: null,
        rangeEndLabel: null,
        activeOption: null,
    });
}
