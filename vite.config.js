import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/agency-dashboard.css', 'resources/css/admin-dashboard.css', 'resources/css/landing.css', 'resources/css/site-v2.css', 'resources/css/pages/landing.css', 'resources/css/pages/discover.css', 'resources/css/pages/hosts.css', 'resources/css/pages/experiences.css', 'resources/css/pages/reviews.css', 'resources/css/pages/experience-detail.css', 'resources/css/pages/experience-detail-v1.css', 'resources/css/pages/host-profile.css', 'resources/css/pages/account.css', 'resources/css/experience-card.css', 'resources/css/experiences.css', 'resources/css/experience-detail.css', 'resources/css/booking-flow.css', 'resources/css/tourist-dashboard.css', 'resources/js/app.js', 'resources/js/site-motion.js'],
            refresh: true,
        }),
    ],
});
