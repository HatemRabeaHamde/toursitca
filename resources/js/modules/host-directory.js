import { liveFilters } from './live-filters.js';

/** Local hosts directory: live filters, top_rated is the default sort. */
export const hostDirectory = () => liveFilters({ defaults: { sort: 'top_rated' } });
