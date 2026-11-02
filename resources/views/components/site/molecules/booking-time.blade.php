<div class="lv-bfield" x-show="store.selectedDate && store.selectedDaySlots.length" x-cloak>
    <label class="lv-t-label-m lv-tone-ink" for="booking-time">{{ __('experience_review.time') }}</label>
    <span class="lv-bfield__control">
        <x-site.atoms.icon name="clock" :size="18" />
        <select id="booking-time" class="lv-bfield__select" :value="store.selectedTime ?? ''" @change="selectTime($event.target.value)" :disabled="store.isStartingCheckout">
            <option value="">{{ __('experience_review.choose_time') }}</option>
            <template x-for="time in timeChoices()" :key="time"><option :value="time" x-text="time" :selected="time === store.selectedTime"></option></template>
        </select>
    </span>
</div>
