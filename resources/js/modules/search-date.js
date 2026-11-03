/**
 * Marketplace search bar · "When" date picker.
 *
 * A small month calendar (Monday first) that sets one date. The chosen day rides in a
 * hidden input named `date`, so the live filters submit it like any other field.
 * Past days are disabled; `min` is today, as the server's date rule expects.
 */
export function searchDate({ value = '', locale = 'en', min }) {
    const pad = (n) => String(n).padStart(2, '0');
    const iso = (date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    const parse = (text) => {
        const [year, month, day] = text.split('-').map(Number);

        return new Date(year, month - 1, day);
    };
    const monthOf = (date) => new Date(date.getFullYear(), date.getMonth(), 1);
    const format = (options, date) => new Intl.DateTimeFormat(locale, options).format(date);

    return {
        open: false,
        value,
        view: monthOf(parse(value || min)),

        get label() {
            return this.value ? format({ day: 'numeric', month: 'short', year: 'numeric' }, parse(this.value)) : '';
        },

        get title() {
            return format({ month: 'long', year: 'numeric' }, this.view);
        },

        get weekdays() {
            return [...Array(7)].map((_, index) => format({ weekday: 'narrow' }, new Date(2024, 0, 1 + index)));
        },

        get cells() {
            const year = this.view.getFullYear();
            const month = this.view.getMonth();
            const blanks = (this.view.getDay() + 6) % 7;
            const days = new Date(year, month + 1, 0).getDate();
            const cells = [...Array(blanks)].map(() => null);

            for (let day = 1; day <= days; day++) {
                const key = iso(new Date(year, month, day));
                cells.push({ day, key, past: key < min, today: key === min });
            }

            return cells;
        },

        canGoBack() {
            return iso(new Date(this.view.getFullYear(), this.view.getMonth(), 0)) >= min;
        },

        shift(months) {
            this.view = new Date(this.view.getFullYear(), this.view.getMonth() + months, 1);
        },

        pick(key) {
            this.value = key;
            this.open = false;
            this.$nextTick(() => this.$refs.input.form.requestSubmit());
        },

        /** Back / forward: the URL's date replaces ours. */
        sync(key) {
            this.value = key;
            this.view = monthOf(parse(key || min));
        },
    };
}
