export function checkoutPickupMap(config = {}) {
    return {
        lat: normalizeCoordinate(config.lat),
        lng: normalizeCoordinate(config.lng),
        map: null,
        marker: null,
        leaflet: null,
        mapReady: false,
        hasPoint: false,

        init() {
            this.hasPoint = this.lat !== '' && this.lng !== '';

            this.$watch('pickupStatus', (value) => {
                if (value === 'add_now') {
                    this.$nextTick(() => this.ensureMapSize());
                }
            });
        },

        async loadMap() {
            if (this.map) {
                this.ensureMapSize();
                return;
            }

            const { default: L } = await import('leaflet');
            this.leaflet = L;

            const center = this.hasPoint ? [Number(this.lat), Number(this.lng)] : [
                normalizeCoordinate(config.defaultLat) || 31.6295,
                normalizeCoordinate(config.defaultLng) || -7.9811,
            ];

            this.map = L.map(this.$refs.pickupMap, {
                scrollWheelZoom: false,
                zoomControl: true,
            }).setView(center, this.hasPoint ? 14 : 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(this.map);

            this.map.on('click', (event) => this.setPoint(event.latlng));

            if (this.hasPoint) {
                this.placeMarker(center);
            }

            this.mapReady = true;
            this.ensureMapSize();
        },

        ensureMapSize() {
            window.setTimeout(() => {
                this.map?.invalidateSize();
            }, 80);
        },

        setPoint(latlng) {
            this.lat = Number(latlng.lat).toFixed(7);
            this.lng = Number(latlng.lng).toFixed(7);
            this.hasPoint = true;
            this.placeMarker([Number(this.lat), Number(this.lng)]);
        },

        placeMarker(position) {
            if (this.marker) {
                this.marker.setLatLng(position);
                return;
            }

            this.marker = this.leaflet.marker(position, {
                draggable: true,
                icon: this.markerIcon(),
            }).addTo(this.map);

            this.marker.on('dragend', () => {
                this.setPoint(this.marker.getLatLng());
            });
        },

        markerIcon() {
            return this.leaflet.divIcon({
                className: 'checkout-map-marker',
                html: '<span></span>',
                iconSize: [30, 30],
                iconAnchor: [15, 30],
            });
        },

        clearPoint() {
            this.lat = '';
            this.lng = '';
            this.hasPoint = false;

            if (this.marker) {
                this.marker.remove();
                this.marker = null;
            }
        },
    };
}

function normalizeCoordinate(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    const number = Number(value);

    return Number.isFinite(number) ? number.toFixed(7) : '';
}
