# Morocco Tourism Platform — SEO Strategy
> This is the authoritative SEO reference for the project. Read before touching any route, controller, or Blade template on public pages.

---

## The Core SEO Opportunity

This platform has a structural advantage: **server-rendered Blade pages with real URLs per experience, city, and category**.
Google can crawl every page instantly. No JS rendering. No hydration lag. No SPA pitfalls.

The target keywords are high-intent and underserved:
- "things to do in Marrakech" → 90k+ searches/month
- "desert tour Morocco" → 40k+ searches/month
- "food tour Marrakech" → 12k+ searches/month
- "what to do in Fes Morocco" → 8k+ searches/month
- Dutch variants: "dingen te doen in Marrakech", "woestijn tour Marokko"
- French variants: "que faire à Marrakech", "excursion désert Maroc"

These are not blog queries. They are transactional. A tourist typing these is ready to book. We must own them.

---

## URL Architecture (Most Important Decision)

The current route structure is wrong for SEO:
```
/{locale}/experiences/{experience}     ← slug only, no city context
```

This must become:
```
/{locale}/experiences/{city}/{slug}
```

### Full URL Structure

| Page | URL | Intent |
|------|-----|--------|
| Homepage | `/en/` | Brand |
| City landing | `/en/experiences/marrakech/` | Informational + Commercial |
| Category landing | `/en/experiences/desert-tours/` | Commercial |
| City + Category | `/en/experiences/marrakech/food-tours/` | Transactional |
| Experience detail | `/en/experiences/marrakech/spice-souk-walking-tour/` | Transactional |
| Search with filters | `/en/search?city=...&category=...` | NOT indexed |

### Why city must be in the URL
`/en/experiences/spice-souk-walking-tour` tells Google nothing about location.
`/en/experiences/marrakech/spice-souk-walking-tour` tells Google this is a Marrakech result.
When someone searches "food tour Marrakech", Google prefers the URL that contains the city.

### Slugs
- Per-locale: English slug `sahara-sunset-camel-trek`, French `trek-chameau-coucher-soleil-sahara`, Dutch `zonsondergang-kameeltocht-sahara`
- Generated from `title` translation via `spatie/laravel-sluggable` (translatable variant)
- Stored in a `slug` JSON column on `experiences` table: `{"en": "...", "fr": "...", "nl": "..."}`
- City slugs: lowercase, hyphenated, no accents — `marrakech`, `fes`, `chefchaouen`, `sahara`
- Category slugs: `desert-tours`, `food-tours`, `cultural-tours`, `mountain-hikes`, `workshops`

---

## Pages That Must Exist for SEO

### 1. City Landing Pages
`/en/experiences/marrakech/` — "Things to Do in Marrakech"

These are the highest-traffic pages. Every major city needs one.

**Must contain:**
- H1: "Things to Do in Marrakech" (EN) / "Que faire à Marrakech" (FR) / "Dingen te doen in Marrakech" (NL)
- Short intro paragraph (2–3 sentences, factual, no fluff)
- Category filter strip (Desert / Food / Cultural / etc.)
- All published experiences in that city (paginated)
- Experience count: "24 experiences in Marrakech"
- JSON-LD: `TouristDestination` + `ItemList`

**Cities for MVP launch (Marrakech first):**
`marrakech`, `fes`, `chefchaouen`, `sahara`, `agadir`, `casablanca`, `essaouira`, `ouarzazate`

### 2. Category Landing Pages
`/en/experiences/desert-tours/` — "Desert Tours in Morocco"

**Must contain:**
- H1: "Desert Tours in Morocco"
- Short description of what this category offers (factual)
- All published experiences in this category across all cities
- JSON-LD: `ItemList`

**Categories:** `desert-tours`, `food-tours`, `cultural-tours`, `mountain-hikes`, `workshops`, `day-trips`

### 3. City + Category Pages
`/en/experiences/marrakech/food-tours/` — "Food Tours in Marrakech"

Only create these when there are ≥ 3 experiences for that combination.
If fewer than 3: redirect to city page or category page, never show a thin page.

### 4. Experience Detail Page
`/en/experiences/marrakech/spice-souk-walking-tour/`

This is the highest-converting page. Every section of it serves SEO or conversion.

**Must contain (for SEO):**
- H1 = experience title (in current locale)
- Price explicitly visible above the fold
- Duration, difficulty, group size visible above fold
- Full description (minimum 200 words for the experience story)
- Itinerary (step-by-step — Google loves structured content)
- Inclusions/exclusions (list format)
- Cancellation policy (plain language)
- Reviews section with real review text
- Agency name + city
- JSON-LD: `Product` + `AggregateRating` + `Review[]` + `BreadcrumbList`

---

## Required Structured Data (JSON-LD)

Never use microdata or RDFa. Always JSON-LD in `<script type="application/ld+json">`.

### Homepage
```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Morocco Tourism",
  "url": "https://example.com",
  "logo": "https://example.com/images/logo.png",
  "sameAs": ["https://instagram.com/...", "https://facebook.com/..."]
}
```

### City Landing Page
```json
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Things to Do in Marrakech",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "url": "https://example.com/en/experiences/marrakech/spice-souk-tour"
    }
  ]
}
```

### Experience Detail Page (most important)
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Spice Souk Walking Tour",
  "description": "...",
  "image": ["https://..."],
  "offers": {
    "@type": "Offer",
    "price": "350",
    "priceCurrency": "MAD",
    "availability": "https://schema.org/InStock",
    "url": "https://example.com/en/experiences/marrakech/spice-souk-tour"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "reviewCount": "23",
    "bestRating": "5",
    "worstRating": "1"
  },
  "review": [
    {
      "@type": "Review",
      "reviewRating": {
        "@type": "Rating",
        "ratingValue": "5"
      },
      "author": {
        "@type": "Person",
        "name": "John D."
      },
      "reviewBody": "Amazing tour, our guide knew every corner of the souk..."
    }
  ]
}
```

Include up to 5 most recent visible reviews in the JSON-LD.
`aggregateRating` uses `experiences.rating_avg` and `experiences.reviews_count` (denormalized — never run AVG() on render).

### BreadcrumbList (every inner page)
```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Home", "item": "/en/"},
    {"@type": "ListItem", "position": 2, "name": "Marrakech", "item": "/en/experiences/marrakech/"},
    {"@type": "ListItem", "position": 3, "name": "Spice Souk Tour", "item": "/en/experiences/marrakech/spice-souk-tour/"}
  ]
}
```

---

## Meta Tags — Rules Per Page Type

### Title Tag
- **Homepage:** `Authentic Moroccan Experiences — Book Local Tours | Morocco Tourism`
- **City page:** `Things to Do in Marrakech — Tours & Experiences | Morocco Tourism`
- **Category page:** `Desert Tours in Morocco — Book Authentic Trips | Morocco Tourism`
- **Experience detail:** `{Title} in {City} — {Duration}h from {Price} MAD | Morocco Tourism`
- Max 60 characters. The experience name + city + price signal is the highest-converting title pattern for booking pages.

### Meta Description
- **City page:** `Discover {count} authentic experiences in Marrakech. Book desert tours, food walks, cultural workshops and more. Verified local agencies. Instant confirmation.`
- **Experience detail:** First 155 chars of the experience description, always ending with a price or duration signal.
- Never duplicate descriptions. Every page gets a unique one.
- Never generate a meta description that does not match the actual page content.

### Canonical
- Every page has a canonical pointing to itself (prevents pagination/filter duplicates).
- `/en/search?city=marrakech` → canonical to `/en/experiences/marrakech/`
- Paginated pages: page 1 has self-canonical, pages 2+ have self-canonical (do NOT point all to page 1 — Google penalizes that now).

### hreflang
Every public page outputs alternates for EN/FR/NL + `x-default` pointing to the English version:
```html
<link rel="alternate" hreflang="en" href="https://example.com/en/experiences/marrakech/spice-tour/" />
<link rel="alternate" hreflang="fr" href="https://example.com/fr/experiences/marrakech/visite-epices/" />
<link rel="alternate" hreflang="nl" href="https://example.com/nl/experiences/marrakech/kruidenwandeling/" />
<link rel="alternate" hreflang="x-default" href="https://example.com/en/experiences/marrakech/spice-tour/" />
```

Slugs must be translated — never use the English slug across all locales.

---

## Sitemap Strategy

Use `spatie/laravel-sitemap`. Run daily via scheduler.

### Sitemap Index
`/sitemap.xml` → index pointing to:
- `/sitemap-static.xml` — homepage, about, how it works, contact
- `/sitemap-cities.xml` — all city landing pages (EN + FR + NL)
- `/sitemap-categories.xml` — all category pages (EN + FR + NL)
- `/sitemap-experiences.xml` — all published experience pages (EN + FR + NL)

### Priorities and Change Frequency
| Page | Priority | changefreq |
|------|----------|-----------|
| Homepage (per locale) | 1.0 | daily |
| City pages | 0.9 | weekly |
| Category pages | 0.9 | weekly |
| Experience detail | 0.8 | weekly |
| Static pages | 0.5 | monthly |

### What to exclude from sitemap
- `/admin/*`
- `/agency/*`
- `/en/search*` (filter URLs)
- `/en/checkout/*`
- `/en/dashboard/*`
- Any experience with `status != published`

---

## robots.txt

```
User-agent: *
Allow: /

Disallow: /admin/
Disallow: /agency/
Disallow: /en/search
Disallow: /fr/search
Disallow: /nl/search
Disallow: /en/checkout/
Disallow: /fr/checkout/
Disallow: /nl/checkout/
Disallow: /en/dashboard/
Disallow: /fr/dashboard/
Disallow: /nl/dashboard/
Disallow: /horizon/

Sitemap: https://example.com/sitemap.xml
```

---

## Core Web Vitals — What to Actually Do

These affect ranking. Blade + Tailwind already gives us a good baseline. The specific rules:

### LCP (Largest Contentful Paint) — target < 2.5s
- The hero image on the experience detail page is the LCP element.
- It must have `<link rel="preload" as="image" href="{{ $heroUrl }}" fetchpriority="high">` in `<head>`.
- Never lazy-load the first image on any page. `loading="lazy"` only on below-fold images.
- Serve WebP. Spatie Media Library handles this via conversions.
- Set explicit `width` and `height` on the hero image — no CLS from unsized images.

### CLS (Cumulative Layout Shift) — target < 0.1
- Every image must have explicit `width` and `height` attributes, or `aspect-ratio` in CSS.
- Never inject content above the fold via Alpine.js on load. Blade renders the initial state.
- Fonts must be loaded with `font-display: swap` and preloaded.

### INP (Interaction to Next Paint) — target < 200ms
- Alpine.js is lightweight — this is not a problem as long as we don't load unnecessary JS.
- Map (Leaflet) must be loaded lazily — only when the user scrolls to the map section.
- No third-party scripts loading synchronously.

---

## Internal Linking Strategy

This is free SEO that most sites ignore.

### Experience Detail Page
Must link to:
- Similar experiences in the same city (city anchor: "More things to do in Marrakech")
- Same category in different cities ("Desert Tours in Morocco")
- The city landing page in breadcrumbs

### City Landing Page
Must link to:
- Top 3 categories available in that city
- Top 6 experiences by rating
- Neighboring cities ("Also explore: Fes, Chefchaouen")

### Homepage
Must link to:
- Top 5 cities
- Top 4 categories
- Top 8 experiences by rating/bookings

### Never
- Do not use generic anchor text like "click here" or "read more"
- Anchor text must describe the destination: "Food Tours in Marrakech", "Sahara Desert Trips"

---

## Image SEO

Every experience image must have:
```html
<img
  src="{{ $experience->thumbnail_url }}"
  alt="{{ $experience->title }} in {{ $experience->location_city }}, Morocco"
  width="800"
  height="600"
  loading="{{ $loop->first ? 'eager' : 'lazy' }}"
/>
```

Rules:
- `alt` always includes the experience name + city + Morocco — matches what people search
- First image on the page: `loading="eager"`, all others: `loading="lazy"`
- Always WebP format via Spatie conversions
- Original filename before upload does not matter — alt text and surrounding context do

---

## What Kills SEO — Hard Rules

| Problem | Why it kills rankings | Rule |
|---------|----------------------|------|
| Thin city pages (< 3 experiences) | Google penalizes low-content pages | Redirect to parent, never show the page |
| Duplicate meta descriptions | Signals low quality | Every page has a unique generated description |
| Search filter URLs indexed | Creates thousands of near-duplicate pages | `noindex` on all `?` URLs or block in robots.txt |
| English slugs across all locales | Weak hreflang signal | Slugs must be translated per locale |
| `aggregateRating` with 0 reviews | Rich snippet penalty | Only output `aggregateRating` in JSON-LD when `reviews_count >= 1` |
| No canonical on paginated pages | Duplicate content | Self-canonical on every page, including page 2+ |
| Images without dimensions | CLS score penalty | Always set `width` + `height` |
| Fabricated review content | Trust + legal issue | Reviews only from confirmed bookings, no fake data ever |
| Agency description keyword stuffing | Manual penalty risk | Descriptions must read naturally — written for humans |
| Blocking CSS/JS in robots.txt | Googlebot can't render | Never block Tailwind or Alpine |

---

## SEO Implementation in Code

### SeoComposer — what it must inject
Every view receives a `$seo` object. The composer must set it from the route/controller context:

```php
// Experience detail — set by ExperienceController@show
$seo = [
    'title'       => $experience->title . ' in ' . $experience->location_city . ' | Morocco Tourism',
    'description' => Str::limit(strip_tags($experience->description), 155),
    'canonical'   => route('site.experiences.show', [$locale, $citySlug, $experienceSlug]),
    'og_image'    => $experience->thumbnail_url,
    'og_type'     => 'product',
    'hreflang'    => [
        'en' => route('site.experiences.show', ['en', $citySlug, $experience->getSlug('en')]),
        'fr' => route('site.experiences.show', ['fr', $citySlug, $experience->getSlug('fr')]),
        'nl' => route('site.experiences.show', ['nl', $citySlug, $experience->getSlug('nl')]),
    ],
    'schema'      => $experienceJsonLd,  // pre-built array, output in schema partial
];
```

The controller builds `$seo` and passes it to the view. The `SeoComposer` only provides the fallback defaults. Page-specific SEO always overrides.

### schema.blade.php partial
```blade
@if(isset($seo['schema']))
    <script type="application/ld+json">{!! json_encode($seo['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endif
```

Use `{!! !!}` here — it is intentional. JSON-LD is not user input. Add a comment in the template.

### Slug column
Add to `experiences` migration:
```php
$table->json('slug')->nullable(); // {"en": "spice-souk-tour", "fr": "visite-souk-epices", "nl": "kruidenwandeling"}
```

Route model binding resolves by locale slug:
```php
// In RouteServiceProvider or model
Route::bind('experience', function ($value) {
    $locale = app()->getLocale();
    return Experience::published()
        ->whereJsonContains("slug->{$locale}", $value)
        ->firstOrFail();
});
```

---

## Route Structure Change Required

Current `routes/site.php` must be updated to add city and city+category routes:

```php
Route::prefix('{locale}')
    ->where(['locale' => $localePattern])
    ->middleware('set.locale')
    ->group(function () {

        // Homepage
        Route::get('/', HomeController::class)->name('site.home');

        // City landing: /en/experiences/marrakech/
        Route::get('/experiences/{city}', [CityController::class, 'show'])
            ->name('site.city.show');

        // Category landing: /en/experiences/desert-tours/
        Route::get('/experiences/{category}', [CategoryController::class, 'show'])
            ->name('site.category.show');
        // Note: city and category both use a single slug segment.
        // Distinguish by checking against known city slugs vs category slugs.
        // OR use separate prefixes: /en/city/marrakech and /en/category/desert-tours

        // City + Category: /en/experiences/marrakech/food-tours/
        Route::get('/experiences/{city}/{category}', [CityController::class, 'category'])
            ->name('site.city.category');

        // Experience detail: /en/experiences/marrakech/spice-souk-tour/
        Route::get('/experiences/{city}/{experience:slug}', [ExperienceController::class, 'show'])
            ->name('site.experiences.show');

        // Booking flow
        Route::get('/experiences/{city}/{experience:slug}/book', [BookingController::class, 'create'])
            ->name('site.bookings.create');
        Route::post('/experiences/{city}/{experience:slug}/book', [BookingController::class, 'store'])
            ->name('site.bookings.store');

        // Booking confirmation
        Route::get('/bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])
            ->name('site.bookings.confirmation');

        // Search (with GET params — not indexed, redirects to city page when city is selected)
        Route::get('/search', [SearchController::class, 'index'])
            ->name('site.search');
    });
```

The ambiguity between `/{city}` and `/{category}` is real. Cleanest solution:
- Keep a `config/seo_cities.php` list of known city slugs
- Middleware/controller resolves: if slug is in city list → city page, else → category page
- OR use separate prefixes: `/en/in/marrakech` vs `/en/category/desert-tours` (simpler routing, slightly longer URLs)

Recommended: separate prefixes are cleaner in code even if URLs are slightly longer.

---

## Multilingual Content Priority

For launch, minimum viable multilingual SEO:
1. **English first** — all experiences must have EN title, description, slug
2. **French second** — highest tourist volume from France/Belgium to Morocco
3. **Dutch third** — Netherlands + Belgium (Dutch-speaking)

If an experience has no French translation yet: do not show a blank page. Fall back to English content but keep the French URL. Add `<meta name="robots" content="noindex">` until translated. Remove it once translated.

Never auto-translate with machine translation and publish. It is worse than English fallback for SEO.

---

## Tracking What Actually Works

Once live, monitor:
- Google Search Console: impressions/clicks per page and query
- Which city pages rank first (focus link-building there)
- Which experiences get impressions but no clicks (fix title/description)
- Core Web Vitals report in GSC (field data, not just lab)
- Sitemap coverage: submitted vs indexed ratio

The first 3 months of data tells you which keywords to double down on.
