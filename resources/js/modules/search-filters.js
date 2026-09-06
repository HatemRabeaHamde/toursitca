export const searchFilters = () => ({
    open: false,
    submit() { this.$root.closest('form').submit(); },
    reset() {
        this.$root.closest('form').querySelectorAll('input, select').forEach(el => {
            if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
            else el.value = '';
        });
        this.submit();
    },
});
