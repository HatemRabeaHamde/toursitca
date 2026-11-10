import './bootstrap';
import 'leaflet/dist/leaflet.css';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import focus from '@alpinejs/focus';

import { searchFilters } from './modules/search-filters.js';
import { experienceFilters } from './modules/experience-filters.js';
import { availabilityPicker } from './modules/availability-picker.js';
import { checkoutWizard } from './modules/checkout.js';
import { mediaGallery } from './modules/media-gallery.js';
import { registerExperienceBookingStore } from './modules/experience-booking-store.js';
import { homeCarousel } from './modules/home-carousel.js';
import { registerPageTransitions } from './modules/page-transition.js';
import { wishlistBtn } from './modules/wishlist.js';
import { checkoutPickupMap } from './modules/checkout-pickup-map.js';
import { coordinatePicker } from './modules/maps/coordinate-picker.js';
import { experienceRouteMap } from './modules/maps/experience-route-map.js';
import { registerCheckoutTransitions } from './modules/checkout-transitions.js';
import { lvHero, lvNewsletter } from './modules/landing-v2.js';
import { discoverMap, guideToc } from './modules/discover.js';
import { hostDirectory } from './modules/host-directory.js';
import { liveFilters } from './modules/live-filters.js';
import { experienceBookingCard } from './modules/experience-booking-card.js';
import { photoViewer } from './modules/photo-viewer.js';
import { searchDate } from './modules/search-date.js';

Alpine.plugin(intersect);
Alpine.plugin(focus);

registerExperienceBookingStore(Alpine);

Alpine.data('searchFilters', searchFilters);
Alpine.data('experienceFilters', experienceFilters);
Alpine.data('availabilityPicker', availabilityPicker);
Alpine.data('checkoutWizard', checkoutWizard);
Alpine.data('mediaGallery', mediaGallery);
Alpine.data('homeCarousel', homeCarousel);
Alpine.data('wishlistBtn', wishlistBtn);
Alpine.data('lvHero', lvHero);
Alpine.data('lvNewsletter', lvNewsletter);
Alpine.data('discoverMap', discoverMap);
Alpine.data('guideToc', guideToc);
Alpine.data('hostDirectory', hostDirectory);
Alpine.data('experienceMarketplace', () => liveFilters({ defaults: { sort: 'recommended' } }));
Alpine.data('reviewDirectory', () => liveFilters({ defaults: { sort: 'recent' } }));
Alpine.data('checkoutPickupMap', checkoutPickupMap);
Alpine.data('coordinatePicker', coordinatePicker);
Alpine.data('experienceRouteMap', experienceRouteMap);
Alpine.data('experienceBookingCard', experienceBookingCard);
Alpine.data('photoViewer', photoViewer);
Alpine.data('searchDate', searchDate);

window.Alpine = Alpine;
Alpine.start();

registerPageTransitions();
registerCheckoutTransitions();
