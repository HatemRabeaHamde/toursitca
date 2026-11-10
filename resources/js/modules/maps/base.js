/**
 * Shared Leaflet setup for every Tourstica map: warm OpenStreetMap tiles,
 * brand pins and a dashed route line. Styles live in resources/css/app.css (.ts-map).
 */
import L from 'leaflet';

export { L };

export const MOROCCO_CENTER = [31.7917, -7.0926];
export const MOROCCO_ZOOM = 6;

const TILE_URL = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
const TILE_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>';

const PIN_SVG = '<svg viewBox="0 0 36 46" aria-hidden="true"><path d="M18 44.5S33.5 30 33.5 18a15.5 15.5 0 1 0-31 0C2.5 30 18 44.5 18 44.5Z"/></svg>';

/** A Leaflet map with the house style. Scroll-wheel zoom waits for a click, so the page keeps scrolling. */
export function createMap(element, options = {}) {
    element.classList.add('ts-map');

    const map = L.map(element, {
        zoomControl: false,
        attributionControl: false,
        scrollWheelZoom: false,
        ...options,
    });

    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.control.attribution({ position: 'bottomleft', prefix: false }).addTo(map);
    L.tileLayer(TILE_URL, { maxZoom: 19, attribution: TILE_ATTRIBUTION }).addTo(map);

    map.once('click', () => map.scrollWheelZoom.enable());

    return map;
}

/** Brand pin: "meet" (forest, white dot) or "stop" (clay, numbered). `muted` fades context pins. */
export function pinIcon({ label = '', variant = 'stop', muted = false } = {}) {
    const text = String(label).replace(/[^0-9A-Za-z]/g, '').slice(0, 2);

    return L.divIcon({
        className: `ts-pin ts-pin--${variant}${muted ? ' ts-pin--muted' : ''}`,
        html: `${PIN_SVG}<span class="ts-pin__label">${text}</span>`,
        iconSize: [36, 46],
        iconAnchor: [18, 45],
        popupAnchor: [0, -40],
    });
}

/** Dashed clay route over a white halo, so it reads on any tile. `target` is a map or a layer group. */
export function drawRoute(target, points, { muted = false } = {}) {
    const group = L.layerGroup().addTo(target);

    if (points.length < 2) {
        return group;
    }

    L.polyline(points, { color: '#fff', weight: 7, opacity: muted ? 0.5 : 0.85, interactive: false }).addTo(group);
    L.polyline(points, {
        color: '#C45D3D',
        weight: 3,
        opacity: muted ? 0.55 : 1,
        dashArray: '1 9',
        lineCap: 'round',
        interactive: false,
    }).addTo(group);

    return group;
}

/** Popup body built from text nodes (names come from agencies, never trusted as HTML). */
export function popupContent(title, kicker = '') {
    const box = document.createElement('div');
    box.className = 'ts-popup';

    if (kicker) {
        const small = document.createElement('span');
        small.className = 'ts-popup__kicker';
        small.textContent = kicker;
        box.appendChild(small);
    }

    const strong = document.createElement('strong');
    strong.textContent = title;
    box.appendChild(strong);

    return box;
}

/** Frame every point; a single point gets a street-level zoom. */
export function frame(map, points, { maxZoom = 16, padding = 56 } = {}) {
    if (points.length === 0) {
        map.setView(MOROCCO_CENTER, MOROCCO_ZOOM);
        return;
    }

    if (points.length === 1) {
        map.setView(points[0], 15);
        return;
    }

    map.fitBounds(L.latLngBounds(points), { padding: [padding, padding], maxZoom });
}

export function toCoordinate(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    const number = Number(value);

    return Number.isFinite(number) ? number : null;
}

/** Same spot (≈ 25 m), so the meeting point and the first stop share one pin. */
export function samePlace(a, b) {
    return Math.abs(a[0] - b[0]) < 0.00025 && Math.abs(a[1] - b[1]) < 0.00025;
}
