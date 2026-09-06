import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/agency-dashboard.css', 'resources/css/admin-dashboard.css', 'resources/css/landing.css', 'resources/css/experience-card.css', 'resources/css/experiences.css', 'resources/css/experience-detail.css', 'resources/css/booking-flow.css', 'resources/css/tourist-dashboard.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
