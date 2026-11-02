import { test } from 'node:test';
import assert from 'node:assert/strict';
import { registerExperienceBookingStore } from '../../resources/js/modules/experience-booking-store.js';

function store() {
    let booking;
    registerExperienceBookingStore({ store(name, value) { booking = value; } });
    booking.configure({ experienceId: 1, quoteUrl: '/quote', csrfToken: 'test-token', maxParticipants: 8 });
    booking.selectDate('2026-11-05', '5 Nov', { 1: 11, 2: 12 });
    booking.activeOption = 1;
    return booking;
}
function response(total) { return { ok: true, json: async () => ({ data: { total } }) }; }

test('a slow quote cannot overwrite the newly selected option', async () => {
    const booking = store();
    const pending = [];
    globalThis.fetch = () => new Promise(resolve => pending.push(resolve));
    const first = booking.applyQuote();
    booking.clearQuote();
    booking.activeOption = 2;
    const second = booking.applyQuote();
    pending[1](response('500.00'));
    await second;
    pending[0](response('100.00'));
    await first;
    assert.equal(booking.activeOption, 2);
    assert.equal(booking.quote.total, '500.00');
    assert.equal(booking.isQuoting, false);
});

test('changing date or party discards in-flight totals and errors', async () => {
    const booking = store();
    let reject;
    globalThis.fetch = () => new Promise((_, no) => { reject = no; });
    const first = booking.applyQuote();
    booking.selectDate('2026-11-06', '6 Nov', { 1: 15 });
    reject(new Error('Old request failed'));
    await first;
    assert.equal(booking.quote, null);
    assert.equal(booking.quoteError, null);
    assert.equal(booking.isQuoting, false);
});

test('a package cannot borrow another package-only availability', () => {
    const booking = store();
    booking.selectedAvailabilityIds = { 2: 12, any: 12 };
    assert.equal(booking.availabilityForOption(1), null);
    assert.equal(booking.availabilityForOption(2), 12);
    booking.selectedAvailabilityIds.default = 13;
    assert.equal(booking.availabilityForOption(1), 13);
});

test('a repeated checkout click creates only one request', async () => {
    const booking = store();
    let complete;
    let requests = 0;
    globalThis.window = { location: {} };
    globalThis.fetch = () => { requests++; return new Promise(resolve => { complete = resolve; }); };
    const first = booking.startCheckout();
    await booking.startCheckout();
    assert.equal(requests, 1);
    complete({ ok: true, json: async () => ({ data: { request_url: '/checkout/one' } }) });
    await first;
    assert.equal(window.location.href, '/checkout/one');
});

import { experienceBookingCard } from '../../resources/js/modules/experience-booking-card.js';
function cardWith(booking, options) {
    const card = experienceBookingCard({ card: { locale: 'en', currency: 'MAD', strings: {} }, store: {}, options });
    card.$store = { experienceBooking: booking };
    card.formatter = { whole: new Intl.NumberFormat('en'), cents: new Intl.NumberFormat('en') };
    return card;
}

test('time and capacity select the exact package slot without changing package silently', async () => {
    const booking = store();
    booking.selectedDaySlots = [
        { id: 11, time: '09:00', optionId: 1, seats: 2, privateOpen: true },
        { id: 12, time: '15:00', optionId: 2, seats: 6, privateOpen: true },
    ];
    booking.selectedTime = '15:00';
    const card = cardWith(booking, [{ id: 1, languages: [] }, { id: 2, languages: [] }]);
    card.syncTimeAvailability();
    await card.syncOptionToDate();
    assert.equal(booking.activeOption, null);
    assert.equal(card.optionAvailable(card.options[0]), false);
    assert.equal(card.optionAvailable(card.options[1]), true);
    booking.adults = 3;
    assert.deepEqual(card.timeChoices(), ['15:00']);
});

test('private badge state remains separate from a per-group price basis', () => {
    const booking = store();
    booking.registerOptionPricing(1, { id: 1, title: 'Group price', priceType: 'per_group', groupPrice: 500, adultPrice: 100 });
    const card = cardWith(booking, []);
    assert.equal(card.isFlat(), true);
    assert.equal(booking.bookingType, 'group');
    assert.equal(card.total(), 500);
    booking.config.privateAvailable = true;
    booking.config.privatePrice = 900;
    booking.setBookingType('private');
    assert.equal(booking.bookingType, 'private');
    assert.equal(card.unitPrice(), 500);
    assert.equal(card.total(), 500);
});

test('draft preview never creates a real checkout', async () => {
    const booking = store();
    booking.config.preview = true;
    globalThis.fetch = () => { throw new Error('Preview must not send a booking request'); };
    await booking.startCheckout();
    assert.equal(booking.isStartingCheckout, false);
});
