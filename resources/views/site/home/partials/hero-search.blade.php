@php
    $locale = app()->getLocale();
    $searchUrl = route('site.experiences.index', ['locale' => $locale]);
    $searchNav ??= [];
    $citiesJson = json_encode(array_values($searchNav['cities'] ?? []), JSON_UNESCAPED_UNICODE);
    $suggestionsJson = json_encode(array_values($searchNav['suggestions'] ?? []), JSON_UNESCAPED_UNICODE);
    $maxParticipants = (int) ($searchNav['max_participants'] ?? config('booking.max_participants'));
@endphp

<div class="hero-search hero-search--xp" id="experiences">
    <form method="GET"
          action="{{ $searchUrl }}"
          class="xp-nav-search hero-xp-search"
          x-data="{
              activePanel: null,
              searchQuery: '',
              selectedCity: '',
              cities: {{ $citiesJson }},
              suggestions: {{ $suggestionsJson }},
              startDate: '',
              endDate: '',
              adults: 1,
              children: 0,
              monthCursor: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
              maxParticipants: {{ $maxParticipants }},
              get totalParticipants() { return this.adults + this.children; },
              get participantsLabel() {
                  return this.totalParticipants === 1
                      ? '{{ __('ui.experience_detail.nav_search.one_participant') }}'
                      : this.totalParticipants + ' {{ __('ui.experience_detail.nav_search.participants') }}';
              },
              get dateLabel() {
                  if (! this.startDate) return '{{ __('ui.experience_detail.nav_search.anytime') }}';
                  if (! this.endDate || this.endDate === this.startDate) return this.formatShortDate(this.startDate);
                  return this.formatShortDate(this.startDate) + ' - ' + this.formatShortDate(this.endDate);
              },
              get filteredCities() {
                  const q = this.searchQuery.toLowerCase();
                  return this.cities.filter((city) => ! q || city.toLowerCase().includes(q)).slice(0, 5);
              },
              open(panel) { this.activePanel = this.activePanel === panel ? null : panel; },
              close() { this.activePanel = null; },
              clearDates() { this.startDate = ''; this.endDate = ''; },
              clearParticipants() { this.adults = 1; this.children = 0; },
              selectCity(city) {
                  this.searchQuery = city;
                  this.selectedCity = city;
                  this.close();
              },
              dateKey(date) {
                  const y = date.getFullYear();
                  const m = String(date.getMonth() + 1).padStart(2, '0');
                  const d = String(date.getDate()).padStart(2, '0');
                  return `${y}-${m}-${d}`;
              },
              formatShortDate(value) {
                  const date = new Date(value + 'T00:00:00');
                  return date.toLocaleDateString('{{ str_replace('_', '-', $locale) }}', { day: 'numeric', month: 'short' });
              },
              monthLabel(offset) {
                  const date = new Date(this.monthCursor.getFullYear(), this.monthCursor.getMonth() + offset, 1);
                  return date.toLocaleDateString('{{ str_replace('_', '-', $locale) }}', { month: 'long', year: 'numeric' });
              },
              monthDays(offset) {
                  const first = new Date(this.monthCursor.getFullYear(), this.monthCursor.getMonth() + offset, 1);
                  const startOffset = (first.getDay() + 6) % 7;
                  const total = new Date(first.getFullYear(), first.getMonth() + 1, 0).getDate();
                  const days = [];
                  for (let i = 0; i < startOffset; i++) days.push(null);
                  for (let day = 1; day <= total; day++) {
                      const current = new Date(first.getFullYear(), first.getMonth(), day);
                      days.push({ number: day, date: this.dateKey(current), isPast: current < new Date(new Date().toDateString()) });
                  }
                  while (days.length % 7 !== 0) days.push(null);
                  return days;
              },
              selectDate(day) {
                  if (! day || day.isPast) return;
                  if (! this.startDate || (this.startDate && this.endDate) || day.date < this.startDate) {
                      this.startDate = day.date;
                      this.endDate = '';
                      return;
                  }
                  this.endDate = day.date;
                  this.close();
              },
              isSelected(day) { return day && (day.date === this.startDate || day.date === this.endDate); },
              isInRange(day) { return day && this.startDate && this.endDate && day.date > this.startDate && day.date < this.endDate; },
              quickDate(type) {
                  const today = new Date();
                  const target = new Date(today);
                  if (type === 'tomorrow') target.setDate(today.getDate() + 1);
                  if (type === 'weekend') target.setDate(today.getDate() + ((6 - today.getDay() + 7) % 7 || 7));
                  this.startDate = this.dateKey(target);
                  this.endDate = '';
                  this.close();
              },
              canAddTraveler() { return this.totalParticipants < this.maxParticipants; },
          }"
          @keydown.escape.window="close()">
        <input type="hidden" :name="selectedCity ? 'city' : ''" :value="selectedCity">
        <input type="hidden" :name="startDate ? 'date_from' : ''" :value="startDate">
        <input type="hidden" :name="endDate ? 'date_to' : ''" :value="endDate">
        <input type="hidden" :name="totalParticipants > 1 ? 'participants' : ''" :value="totalParticipants">

        <div class="xp-nav-field xp-nav-field--search">
            <input
                id="home-search"
                :name="searchQuery.trim() ? 'search' : ''"
                type="text"
                x-model="searchQuery"
                @focus="open('search')"
                @input="selectedCity = ''; activePanel = 'search'"
                placeholder="{{ __('ui.experience_detail.nav_search.placeholder') }}"
                autocomplete="off"
            >
        </div>

        <button type="button" class="xp-nav-chip xp-nav-chip--date" @click="open('date')" :class="{ 'is-selected': startDate }">
            <span x-text="dateLabel"></span>
            <span class="xp-nav-chip-icon" x-show="! startDate" aria-hidden="true">⌄</span>
            <span class="xp-nav-chip-clear" x-show="startDate" x-cloak @click.stop="clearDates()" aria-hidden="true">×</span>
        </button>

        <button type="button" class="xp-nav-chip xp-nav-chip--pax" @click="open('participants')" :class="{ 'is-selected': totalParticipants > 1 }">
            <span x-text="participantsLabel"></span>
            <span class="xp-nav-chip-icon" x-show="totalParticipants === 1" aria-hidden="true">⌄</span>
            <span class="xp-nav-chip-clear" x-show="totalParticipants > 1" x-cloak @click.stop="clearParticipants()" aria-hidden="true">×</span>
        </button>

        <button type="submit" class="xp-nav-submit">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <span>{{ __('ui.landing.search.submit') }}</span>
        </button>

        <div class="xp-nav-panel xp-nav-panel--search" x-show="activePanel === 'search'" x-cloak x-transition>
            <div class="xp-nav-panel-section" x-show="filteredCities.length > 0">
                <p>{{ __('ui.experience_detail.nav_search.destinations') }}</p>
                <template x-for="city in filteredCities" :key="city">
                    <button type="button" class="xp-nav-suggestion" @click="selectCity(city)">
                        <span class="xp-nav-suggestion-icon" aria-hidden="true">⌖</span>
                        <span>
                            <strong x-text="city"></strong>
                            <em>{{ __('ui.experience_detail.nav_search.destination_hint') }}</em>
                        </span>
                    </button>
                </template>
            </div>

            <div class="xp-nav-panel-section" x-show="suggestions.length > 0">
                <p>{{ __('ui.experience_detail.nav_search.suggestions') }}</p>
                <template x-for="item in suggestions" :key="item.category">
                    <a class="xp-nav-suggestion" :href="item.url">
                        <span class="xp-nav-suggestion-thumb" aria-hidden="true"></span>
                        <span>
                            <strong x-text="item.label"></strong>
                            <em x-text="item.count + ' {{ __('ui.labels.activities_count_short') }}' + (item.city ? ' · ' + item.city : '')"></em>
                        </span>
                    </a>
                </template>
            </div>
        </div>

        <div class="xp-nav-panel xp-nav-panel--date" x-show="activePanel === 'date'" x-cloak x-transition>
            <div class="xp-nav-date-quick">
                <button type="button" @click="quickDate('today')">{{ __('ui.experience_detail.nav_search.today') }}</button>
                <button type="button" @click="quickDate('tomorrow')">{{ __('ui.experience_detail.nav_search.tomorrow') }}</button>
                <button type="button" @click="quickDate('weekend')">{{ __('ui.experience_detail.nav_search.next_weekend') }}</button>
            </div>

            <div class="xp-nav-calendars">
                <button type="button" class="xp-nav-month-btn is-prev" @click="monthCursor = new Date(monthCursor.getFullYear(), monthCursor.getMonth() - 1, 1)" aria-label="{{ __('ui.experience_detail.previous_month') }}">‹</button>
                <button type="button" class="xp-nav-month-btn is-next" @click="monthCursor = new Date(monthCursor.getFullYear(), monthCursor.getMonth() + 1, 1)" aria-label="{{ __('ui.experience_detail.next_month') }}">›</button>

                <template x-for="offset in [0, 1]" :key="offset">
                    <div class="xp-nav-calendar">
                        <h3 x-text="monthLabel(offset)"></h3>
                        <div class="xp-nav-weekdays">
                            @foreach (__('ui.experience_detail.weekdays_short') as $weekday)
                                <span>{{ $weekday }}</span>
                            @endforeach
                        </div>
                        <div class="xp-nav-days">
                            <template x-for="(day, index) in monthDays(offset)" :key="offset + '-' + index">
                                <button type="button"
                                        :disabled="! day || day.isPast"
                                        :class="{
                                            'is-empty': ! day,
                                            'is-selected': isSelected(day),
                                            'is-range': isInRange(day),
                                            'is-range-start': day && day.date === startDate && endDate,
                                            'is-range-end': day && day.date === endDate && startDate,
                                        }"
                                        @click="selectDate(day)">
                                    <span x-text="day ? day.number : ''"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="xp-nav-panel xp-nav-panel--pax" x-show="activePanel === 'participants'" x-cloak x-transition>
            <div class="xp-nav-counter">
                <span>
                    <strong>{{ __('ui.experience_detail.booking_panel.adults') }}</strong>
                    <em>{{ __('ui.experience_detail.booking_panel.adults_age') }}</em>
                </span>
                <div>
                    <button type="button" :disabled="adults <= 1" @click="adults = Math.max(1, adults - 1)">−</button>
                    <strong x-text="adults"></strong>
                    <button type="button" :disabled="! canAddTraveler()" @click="if (canAddTraveler()) adults++">+</button>
                </div>
            </div>
            <div class="xp-nav-counter">
                <span>
                    <strong>{{ __('ui.experience_detail.booking_panel.children') }}</strong>
                    <em>{{ __('ui.experience_detail.booking_panel.children_age') }}</em>
                </span>
                <div>
                    <button type="button" :disabled="children <= 0" @click="children = Math.max(0, children - 1)">−</button>
                    <strong x-text="children"></strong>
                    <button type="button" :disabled="! canAddTraveler()" @click="if (canAddTraveler()) children++">+</button>
                </div>
            </div>
            <button type="button" class="xp-nav-apply" @click="close()">{{ __('ui.experience_detail.booking_panel.apply') }}</button>
        </div>
    </form>
</div>
