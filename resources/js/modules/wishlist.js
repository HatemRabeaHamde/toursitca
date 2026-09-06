export function wishlistBtn({ isSaved, toggleUrl }) {
    return {
        isSaved,
        toggleUrl,
        loading: false,

        async toggle() {
            if (this.loading) return;
            this.loading = true;

            try {
                const res = await fetch(this.toggleUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                // 401 = not authenticated → show login modal
                if (res.status === 401) {
                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                    return;
                }

                const contentType = res.headers.get('content-type') || '';
                if (!res.ok || !contentType.includes('application/json')) {
                    throw new Error('Unexpected response');
                }

                const data = await res.json();
                this.isSaved = data.saved;
            } catch (e) {
                console.error('[wishlist]', e);
            } finally {
                this.loading = false;
            }
        },
    };
}
