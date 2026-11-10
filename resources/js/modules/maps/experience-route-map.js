/**
 * Experience detail · real map of the meeting point and the itinerary stops.
 * Loaded on first sight (x-intersect), so tiles never cost anything above the fold.
 *
 * config: { meet: {lat, lng}|null, stops: [{name, lat, lng}], labels: {meet, stop} }
 */
import { L, createMap, drawRoute, frame, pinIcon, popupContent, samePlace } from './base.js';

export function experienceRouteMap(config = {}) {
    return {
        map: null,

        load() {
            if (this.map) {
                return;
            }

            const labels = config.labels ?? {};
            const stops = (config.stops ?? []).map((stop) => ({ ...stop, point: [stop.lat, stop.lng] }));
            const meet = config.meet ? [config.meet.lat, config.meet.lng] : null;
            const meetIsFirstStop = meet !== null && stops.length > 0 && samePlace(meet, stops[0].point);

            this.map = createMap(this.$refs.canvas, { dragging: !L.Browser.mobile, tap: false });

            drawRoute(this.map, stops.map((stop) => stop.point));

            stops.forEach((stop, index) => {
                const isMeet = index === 0 && meetIsFirstStop;
                const kicker = isMeet ? `${labels.meet} · ${labels.stop} ${index + 1}` : `${labels.stop} ${index + 1}`;

                L.marker(stop.point, {
                    icon: pinIcon({ label: index + 1, variant: isMeet ? 'meet' : 'stop' }),
                    title: stop.name,
                    keyboard: true,
                    zIndexOffset: isMeet ? 500 : 0,
                }).bindPopup(popupContent(stop.name, kicker)).addTo(this.map);
            });

            if (meet && !meetIsFirstStop) {
                L.marker(meet, { icon: pinIcon({ variant: 'meet' }), title: labels.meet, keyboard: true, zIndexOffset: 500 })
                    .bindPopup(popupContent(config.meetName || labels.meet, config.meetName ? labels.meet : ''))
                    .addTo(this.map);
            }

            frame(this.map, [...(meet && !meetIsFirstStop ? [meet] : []), ...stops.map((stop) => stop.point)]);
        },

        destroy() {
            this.map?.remove();
            this.map = null;
        },
    };
}
