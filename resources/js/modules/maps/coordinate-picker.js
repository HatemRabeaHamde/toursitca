/**
 * Experience form · pick a point by clicking, dragging the pin or searching a place.
 *
 * config:
 *   lat, lng      initial coordinates (strings or numbers, may be empty)
 *   inputs        [latId, lngId] plain inputs to keep in sync (meeting point)
 *   onPick        (lat, lng) => void, for x-model rows (itinerary stops)
 *   follow/read   scope key to watch + () => [lat, lng], so typed coordinates move the pin
 *   variant       'meet' | 'stop' pin colour
 *   lazy          true = the map is built on toggle(), not on sight
 *   labels        { noResults, searchFailed, stop }
 *
 * Search uses OpenStreetMap Nominatim: one request per explicit search (Enter or
 * the button), never per keystroke, as its usage policy requires.
 */
import { L, MOROCCO_CENTER, MOROCCO_ZOOM, createMap, drawRoute, pinIcon, popupContent, toCoordinate } from './base.js';

const SEARCH_URL = 'https://nominatim.openstreetmap.org/search';
const SEARCH_GAP_MS = 1100;

export function coordinatePicker(config = {}) {
    const [latId, lngId] = config.inputs ?? [];
    const field = (id) => (id ? document.getElementById(id) : null);

    return {
        open: !config.lazy,
        map: null,
        marker: null,
        routeLayer: null,
        route: [],
        lat: toCoordinate(config.lat ?? field(latId)?.value),
        lng: toCoordinate(config.lng ?? field(lngId)?.value),
        query: '',
        results: [],
        searching: false,
        message: '',
        lastSearchAt: 0,

        get hasPoint() {
            return this.lat !== null && this.lng !== null;
        },

        get readout() {
            return this.hasPoint ? `${this.lat.toFixed(5)}, ${this.lng.toFixed(5)}` : '';
        },

        init() {
            [field(latId), field(lngId)].forEach((input) => input?.addEventListener('change', () => this.syncFromInputs()));

            if (config.follow && config.read) {
                this.$watch(config.follow, () => this.moveTo(...config.read()));
            }
        },

        async toggle() {
            this.open = !this.open;

            if (this.open) {
                await this.$nextTick();
                this.load();
            }
        },

        load() {
            if (this.map) {
                window.setTimeout(() => this.map?.invalidateSize(), 60);
                return;
            }

            this.map = createMap(this.$refs.canvas);
            this.map.on('click', (event) => this.pick(event.latlng));

            if (this.hasPoint) {
                this.map.setView([this.lat, this.lng], 14);
                this.placeMarker();
            } else {
                this.map.setView(MOROCCO_CENTER, MOROCCO_ZOOM);
            }

            this.drawContext();
            window.setTimeout(() => this.map?.invalidateSize(), 60);
        },

        pick({ lat, lng }, zoom = null) {
            this.lat = Number(lat);
            this.lng = Number(lng);
            this.message = '';
            this.write();
            this.placeMarker();

            if (zoom) {
                this.map?.setView([this.lat, this.lng], zoom);
            }
        },

        clear() {
            this.lat = null;
            this.lng = null;
            this.write();
            this.marker?.remove();
            this.marker = null;
        },

        write() {
            const lat = this.hasPoint ? this.lat.toFixed(7) : '';
            const lng = this.hasPoint ? this.lng.toFixed(7) : '';

            if (field(latId)) field(latId).value = lat;
            if (field(lngId)) field(lngId).value = lng;
            config.onPick?.(lat, lng);
        },

        placeMarker() {
            if (!this.map || !this.hasPoint) {
                return;
            }

            if (this.marker) {
                this.marker.setLatLng([this.lat, this.lng]);
                return;
            }

            this.marker = L.marker([this.lat, this.lng], {
                icon: pinIcon({ variant: config.variant ?? 'meet' }),
                draggable: true,
                zIndexOffset: 1000,
            }).addTo(this.map);

            this.marker.on('dragend', () => this.pick(this.marker.getLatLng()));
        },

        syncFromInputs() {
            this.moveTo(field(latId)?.value, field(lngId)?.value);
        },

        moveTo(rawLat, rawLng) {
            const lat = toCoordinate(rawLat);
            const lng = toCoordinate(rawLng);

            if (lat === null || lng === null || (this.hasPoint && lat.toFixed(7) === this.lat.toFixed(7) && lng.toFixed(7) === this.lng.toFixed(7))) {
                return;
            }

            this.lat = lat;
            this.lng = lng;
            this.placeMarker();
            this.map?.setView([lat, lng], Math.max(this.map.getZoom(), 13));
        },

        /** Itinerary stops as faded context on the meeting-point map. */
        setRoute(stops) {
            this.route = Array.isArray(stops) ? stops : [];
            this.drawContext();
        },

        drawContext() {
            if (!this.map) {
                return;
            }

            this.routeLayer?.remove();
            this.routeLayer = L.layerGroup().addTo(this.map);

            const points = this.route.map((stop) => [stop.lat, stop.lng]);
            drawRoute(this.routeLayer, points, { muted: true });

            this.route.forEach((stop) => {
                L.marker([stop.lat, stop.lng], { icon: pinIcon({ label: stop.number, muted: true }), interactive: true })
                    .bindPopup(popupContent(stop.name || `${config.labels?.stop ?? 'Stop'} ${stop.number}`, `${config.labels?.stop ?? 'Stop'} ${stop.number}`))
                    .addTo(this.routeLayer);
            });
        },

        async search() {
            const query = this.query.trim();

            if (query.length < 3 || this.searching || Date.now() - this.lastSearchAt < SEARCH_GAP_MS) {
                return;
            }

            this.searching = true;
            this.message = '';
            this.results = [];
            this.lastSearchAt = Date.now();

            try {
                const params = new URLSearchParams({ q: query, format: 'jsonv2', limit: '5', countrycodes: 'ma', 'accept-language': 'en' });
                const response = await fetch(`${SEARCH_URL}?${params}`, { headers: { Accept: 'application/json' } });

                if (!response.ok) {
                    throw new Error(String(response.status));
                }

                const rows = await response.json();
                this.results = rows.map((row) => ({ id: row.place_id, name: row.display_name, lat: Number(row.lat), lng: Number(row.lon) }));
                this.message = this.results.length ? '' : (config.labels?.noResults ?? '');
            } catch {
                this.message = config.labels?.searchFailed ?? '';
            } finally {
                this.searching = false;
            }
        },

        choose(result) {
            this.results = [];
            this.query = result.name.split(',')[0];
            this.pick({ lat: result.lat, lng: result.lng }, 16);
        },

        destroy() {
            this.map?.remove();
            this.map = null;
        },
    };
}
