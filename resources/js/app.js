import './bootstrap';
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

Alpine.plugin(intersect);
Alpine.plugin(focus);

registerExperienceBookingStore(Alpine);

Alpine.data('searchFilters', searchFilters);
Alpine.data('experienceFilters', experienceFilters);
Alpine.data('availabilityPicker', availabilityPicker);
Alpine.data('checkoutWizard', checkoutWizard);
Alpine.data('mediaGallery', mediaGallery);
Alpine.data('homeCarousel', homeCarousel);

window.Alpine = Alpine;
Alpine.start();
