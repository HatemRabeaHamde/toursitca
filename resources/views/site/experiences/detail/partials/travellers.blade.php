{{-- Travellers field + popover (adults, children, child ages). Lives inside experienceBookingCard. --}}
<div class="lv-bfield relative" @click.outside="closeGuests()">
    <span class="lv-t-label-s lv-tone-ink" id="booking-travellers-label">{{ __('experiences.detail.booking.travellers') }}</span>
    <button type="button" class="lv-bfield__control" @click="guestsOpen ? closeGuests() : (guestsOpen = true)"
            aria-labelledby="booking-travellers-label" aria-controls="booking-travellers" :aria-expanded="guestsOpen.toString()">
        <x-site.atoms.icon name="user-terracotta" :size="18" />
        <span class="lv-bfield__value" x-text="travellersLabel()">{{ trans_choice('experiences.detail.booking.adults_value', 1, ['count' => 1]) }}</span>
        <x-site.atoms.icon name="chevron-down-muted" :size="16" class="lv-bfield__chevron" ::class="{ 'is-open': guestsOpen }" />
    </button>

    <div id="booking-travellers" class="lv-popover" x-show="guestsOpen" x-cloak x-transition.opacity.duration.200ms>
        <x-site.molecules.stepper-row :label="__('experiences.detail.booking.adults')" :hint="__('experiences.detail.booking.adults_hint')"
            value="store.adults" can-decrease="store.adults > 1" can-increase="canAdd()"
            decrease="changeAdults(-1)" increase="changeAdults(1)" />
        <x-site.molecules.stepper-row :label="__('experiences.detail.booking.children')" :hint="__('experiences.detail.booking.children_hint')"
            value="store.children" can-decrease="store.children > 0" can-increase="canAdd()"
            decrease="changeChildren(-1)" increase="changeChildren(1)" />

        <template x-if="store.children > 0">
            <div class="flex flex-col gap-2.5 border-t border-[color:var(--lv-line)] pt-3">
                <p class="lv-t-label-s lv-tone-ink">{{ __('experiences.detail.booking.child_ages') }}</p>
                <template x-for="(age, index) in store.childAges" :key="index">
                    <label class="flex items-center justify-between gap-3 lv-t-body-s lv-tone-ink">
                        <span x-text="@js(__('experiences.detail.booking.child', ['number' => '#'])).replace('#', index + 1)"></span>
                        <select class="lv-field lv-popover__select" :value="age ?? ''" @change="setChildAge(index, $event.target.value)">
                            <option value="">{{ __('experiences.detail.booking.age') }}</option>
                            <option value="0">{{ __('experiences.detail.booking.age_under_one') }}</option>
                            @for($age = 1; $age <= 17; $age++)
                                <option value="{{ $age }}">{{ trans_choice('experiences.detail.booking.age_years', $age, ['count' => $age]) }}</option>
                            @endfor
                        </select>
                    </label>
                </template>
            </div>
        </template>

        <x-site.atoms.button variant="outline" :arrow="false" class="w-full" @click="closeGuests()">{{ __('experiences.detail.booking.done') }}</x-site.atoms.button>
    </div>
</div>
