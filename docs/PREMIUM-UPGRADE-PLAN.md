# Premium Morocco Experience Platform — Upgrade Plan

> Source documents: `Morocco-Experience-Platform.pdf` (vision), `Landing-Page-Strategy.pdf` (front door), `Discover-Morocco.pdf` (discovery depth).
> Status: **PLAN ONLY — no code written yet.** Nothing in this document is implemented until approved.

---

## 0. How the three documents relate

They are not three feature lists. They are three zoom levels of one product strategy.

| Level | Document | Question it answers | Output |
|---|---|---|---|
| **L1 — Strategy** | Morocco Experience Platform | *What is this product, and why is it not GetYourGuide?* | Positioning: Booking + Cultural Discovery + Trusted Partners + Community + Trip Planning. Phases 1/2/3. |
| **L2 — Front door** | Landing Page Strategy | *How does a first-time visitor emotionally enter the product?* | The 7-section landing page, journey `Inspire → Discover → Trust → Experience → Book`. |
| **L3 — Depth** | Discover Morocco | *What is behind the "Explore Morocco" / "Explore More" door?* | Discovery hub, interactive map, City pages, Interest pages, multi-path navigation. |

**The dependency chain is strict:**

```
L1 defines the promise ("We don't just help people book Morocco. We help them experience Morocco.")
   ↓
L2 makes the promise felt in 8 seconds on the landing page
   ↓
L3 makes the promise true — the actual content that proves it
   ↓
Existing booking engine converts it (already built, do not break)
```

Practical consequence: **we cannot build L2 properly without L3 content existing**, and we should not build L3 without L1's trust layer (verified partners), otherwise Discover pages send users to unverified providers. This drives the phase order in §4.

---

## 1. Current state audit

### Stack (locked by `CLAUDE.md`)
Laravel 13 + Blade + Tailwind + Alpine.js. MySQL. Server-rendered. **No React / Vue / Inertia / Livewire.**
> Note: the global engineering rules mention React/Zustand/NestJS. Those do **not** apply here — this repo's own instructions override them. Same SOLID/layering spirit is kept: Controller → Service → Model, one job per file, zero hardcoded strings.

### Domain layer (good foundation, keep it)
```
app/Domain/{Agency,Booking,Checkout,Experience,Landing,Payment,Payout,Review,Shared,User,Wishlist}
```
`Domain/Landing` already has: `Destination`, `ExperienceCategory`, `Landmark`, `LandingFaq`, `TravelReel`, `Testimonial`, `NewsletterSubscriber` + `LandingContentService`. Admin CRUD screens already exist for all of them (`resources/views/admin/landing/*`).

### Current landing page order (`site/home.blade.php` + `home/partials/sections.blade.php`)
```
Hero (image slideshow) + SEARCH FORM
Trending / Newest / Top-rated tabs (carousel)
Categories → Deals → Destinations → Agencies → Reels →
Process → Dishes → Testimonials → Agency CTA → FAQ → Newsletter
```

### Target landing page order (client-defined)
```
HERO (cinematic video) → EXPLORE MOROCCO (6-grid) → FEATURED EXPERIENCES →
TRUSTED PARTNERS → HOW IT WORKS → COMMUNITY → FOOTER
```

### Gap analysis

| Current | Target | Action |
|---|---|---|
| Hero image slideshow + big search form | Cinematic video hero, single CTA "Explore Morocco" | **Rebuild.** Search moves out of hero → lives on `/experiences` + nav. |
| Categories (8, carousel-ish) | Explore Morocco — 6 curated cards + "Explore More" | **Reshape** (`is_featured` flag on `experience_categories`). |
| Trending tabs carousel | Featured Experiences (emotive, animated cards) | **Restyle + recopy**, keep the query service. |
| Agencies logo row | Trusted Partners — 5 trust pillars + badges + stats | **Rebuild + new schema.** |
| Process | How It Works — 4 steps | **Restyle**, recopy to the doc's 4 steps. |
| Reels + Testimonials + Dishes | Community Preview | **Merge** into one section; Dishes moves into Discover/Food. |
| Deals / Agency CTA / FAQ | not in the 7-section story | **Demote** — FAQ + newsletter to footer zone, Deals to `/experiences`, Agency CTA to `/partners`. |
| `/morocco-compass` (stub page) | — | Becomes the seed of `/discover` or is deleted. |
| — | Discover hub, City pages, Interest pages, interactive map | **Net new (L3).** |
| — | Verified-agency badges, quality metrics | **Net new (L1 Phase 1).** |
| — | Community stories/photos | **Net new (L1 Phase 1, later slice).** |

### What must NOT be touched
Checkout session flow, booking creation, payments/webhooks, availability & pricing, payouts, reviews write path, agency dashboard, admin approval flows. These are working business logic. Every change below is **additive** around them.

---

## 2. Design foundations (do this first — it is what makes it feel "premium")

Currently colors/spacing live partly in `resources/css/tokens.css` + `landing.css`. Before any section is rebuilt:

1. **Token pass** — a Moroccan warm palette as CSS custom properties + Tailwind theme extension: sand, clay, terracotta, saffron, majorelle blue, zellige green, night ink. Zero hex codes in Blade.
2. **Sectional atmosphere rule** — the docs explicitly reject "repeating identical white sections". Each section gets its own ground: dark cinematic hero → light sand Explore → deep ink Experiences → light Trust → pattern-textured How It Works → warm Community.
3. **Motion system** — one shared Alpine/IntersectionObserver directive for reveal-on-scroll, one for parallax, one for hover-zoom on cards. `prefers-reduced-motion` respected globally. `@alpinejs/intersect` is already installed.
4. **Typography scale** — display serif for headlines, humanist sans for body; fluid `clamp()` sizes, no hardcoded px.
5. **Zellige/pattern asset set** — SVG patterns as reusable Blade components, referenced from a constants file, never inline paths.
6. **Image policy** — every hero/card image responsive (`srcset` + AVIF/WebP), `width`/`height` set to kill CLS, lazy below the fold.

Deliverable: `docs/DESIGN-SYSTEM.md` + `resources/css/tokens.css` rewrite + `resources/views/components/ui/*` primitives.

---

## 3. Data model & routing changes

### 3.1 New domain: `app/Domain/Discovery`

| Model | Table | Purpose | Notes |
|---|---|---|---|
| `City` | `cities` | City Details Page | slug, name(json), tagline(json), region, hero_image, hero_video, lat, lng, best_season, climate, famous_for(json), recommended_days, about(json), is_featured, sort_order |
| `Interest` | reuse `experience_categories` | Explore by Interest | add: `is_featured`, `hero_image`, `intro`(json), `history`(json) |
| `CityHighlight` | reuse `landmarks` | "Must Visit Places" | already city-scoped — extend with `description`(json) + `type` |
| `Dish` | `dishes` | Local Food | name, story, ingredients, best_places, city_id |
| `Craft` | `crafts` | Shopping & Handcrafts | name, story, how_made, where_to_buy |
| `TravelTip` | `travel_tips` | Local Travel Tips (Phase 2) | category, body |
| `CommunityStory` | `community_stories` | Community | user_id, city_id, title, body, photos, status (moderated) |

All text fields are **JSON translatable columns** (`spatie/laravel-translatable`, `{en,fr,nl}`) — matches existing convention. All UI chrome goes in `lang/{en,fr,nl}/ui.php`.

Relations: `City ↔ Experience` (by `location_city` today → migrate to `city_id` FK with a backfill migration), `Interest ↔ Experience` (category), `City ↔ Interest` (pivot: "best cities for Food").

### 3.2 Agency / trust changes (`agencies` table)
Add: `verified_at`, `license_number`, `badges` (json: verified_agency, licensed_business, trusted_local_partner, top_rated, premium_partner), `quality_score`, `last_reviewed_at`. Admin gets a **Verification** screen; badges render on experience cards, experience detail, city pages, and the Trusted Partners section.

### 3.3 Routes (`routes/site.php`) — all additive, all locale-prefixed
```
GET /{locale}/discover                        site.discover.index
GET /{locale}/discover/cities/{city:slug}     site.discover.cities.show
GET /{locale}/discover/interests/{i:slug}     site.discover.interests.show
GET /{locale}/community                       site.community.index      (Phase 5)
GET /{locale}/partners                        site.partners.index       (trust page)
```
Existing `/experiences`, `/experiences/{slug}`, `/checkout/*`, `/dashboard/*` unchanged. Breadcrumbs (`Home → Discover Morocco → Food → Marrakech → Cooking Class`) added as a shared component with JSON-LD for SEO.

---

## 4. Phased execution plan

Each phase is independently shippable and leaves the site working.

### Phase 0 — Foundations *(no visible change)*
Design tokens, motion utilities, UI primitives, image component, breadcrumb component, `docs/DESIGN-SYSTEM.md`.
**Files:** `resources/css/tokens.css`, `tailwind.config.js`, `resources/views/components/ui/*`, `resources/js/motion.js`.
**Risk:** low. **Why first:** every later phase consumes these; skipping this is how we end up with hardcoded colors again.

### Phase 1 — Landing page rebuild (L2)
The 7 sections, in order, with the atmosphere rule applied.
1. **Hero** — full-bleed muted autoplay video (poster fallback, `<video>` with AVIF poster, mobile = poster image only for performance), animated scene captions, headline *"Experience Morocco Like Never Before"*, single primary CTA *"Explore Morocco"* → `/discover`. **Search form removed from hero.**
2. **Explore Morocco** — 6-card grid (Cuisine, Culture & Traditions, History & Heritage, Nature, Adventure, Shopping & Handcrafts) + secondary CTA *"Explore More"* → `/discover`.
3. **Featured Experiences** — reuse `ExperienceLandingPageService`, new emotive card design (hover zoom, micro-animation, short line of desire copy, not a price-first booking card).
4. **Trusted Partners** — 5 pillars (Verified Agencies, Licensed Businesses, Trusted Local Experts, Quality Standards, Continuous Monitoring) + live stats (verified partners, satisfaction, experiences, quality rating).
5. **How It Works** — 4 steps: Discover Morocco → Choose Experiences → Experience Morocco → Share Your Story.
6. **Community Preview** — traveler stories/photo wall (seeded content until Phase 5 ships real UGC) + join CTA.
7. **Footer** — About, Contact, Help, FAQs, Privacy, Terms, Social, Newsletter.

**Files:** `site/home.blade.php` rewritten; new `site/home/partials/{hero-video,explore-morocco,featured-experiences,trusted-partners,how-it-works,community}.blade.php`; old partials (`hero-search`, `deals`, `dishes`, `agency-cta`, `reels`) retired or relocated; `LandingContentService` gains `featuredInterests()`, `trustStats()`, `communityHighlights()`; `lang/*/ui.php` new `landing.*` keys.
**Depends on:** Phase 0, plus seeded content for the 6 interests. **Risk:** medium (highest-visibility page). **Video asset is a client dependency** — the doc recommends real stock footage + cinematic edit, not fully AI-generated.

### Phase 2 — Discover Morocco hub (L3 core)
`/discover`: interactive Morocco map (Leaflet — **already in `package.json`** — with a custom styled tile layer / GeoJSON regions), city pins with hover tooltip (image, landmark, signature dish, popular experience), scrollable city list beside it on desktop, collapsible below on mobile; Popular Cities cards; Explore by Interest grid; Featured Experiences; Community Highlights; Explore CTA.
**Files:** new `Http/Controllers/Site/DiscoverController`, `Domain/Discovery/Services/DiscoveryPageService`, `resources/views/site/discover/index.blade.php` + partials, `resources/css/discover.css`, `resources/js/morocco-map.js`.
**Depends on:** Phase 0 + `cities` table seeded. **Risk:** medium — map performance and mobile UX are the traps; map JS must be lazy-loaded and never block LCP.

### Phase 3 — City Details Pages
Mini landing page per city: Hero (image/video + tagline) → Quick Info (region, season, climate, famous for, duration) → About → Must Visit Places → **Local Experiences (primary conversion)** → Local Food → Shopping & Handcrafts → Community Stories → Related Cities. Sticky sidebar nav on desktop + breadcrumbs.
**Files:** `CityController`, `CityPageService`, `site/discover/city.blade.php` + ~9 partials, admin CRUD for cities/dishes/crafts.
**Depends on:** Phase 2. **Risk:** medium — content volume is the real cost, not the code.

### Phase 4 — Interest Details Pages
Per-interest page: Hero → History & Culture → Signature Experiences → Best Cities → Community Reviews. Completes the `Interest → City → Experience → Booking` path.
**Depends on:** Phase 3 (reuses its components).

### Phase 5 — Trust layer + Experience page upgrade (L1 Phase 1)
Agency verification schema + admin verification workflow; badges surfaced everywhere; experience detail page gains a **Trusted Partner block** and a stronger Community Reviews block; `/partners` page.
**Note:** ideally shipped alongside Phase 1 since the landing page *claims* verification — at minimum the schema + admin flow must exist before Trusted Partners goes live with real numbers.

### Phase 6 — Community
Public traveler profiles, travel stories, photo uploads, recommendations, moderation queue in admin. Replaces seeded community content with real UGC.

### Phase 7 — Future (L1 Phase 2/3, not now)
Book-a-Complete-Trip (multi-item itinerary cart), AI Trip Planner, Smart Budget Planner, Nearby Recommendations, Local Travel Tips, Sports/Wellness verticals, Morocco Passport gamification. **Do not start these until Phases 1–6 are live.**

---

## 5. Cross-cutting requirements (apply to every phase)

- **Localization:** zero hardcoded user-facing strings. UI → `lang/{en,fr,nl}/ui.php`; content → JSON translatable columns. (Repo rule: en/fr/nl only.)
- **Performance:** hero video ≤ ~3–5 MB, `preload="none"` + poster, mobile falls back to image; responsive `srcset`; map JS lazy; target LCP < 2.5s, CLS ≈ 0. Every new query eager-loaded and cached through a Service (`Cache::remember`), never N+1 in Blade.
- **Architecture:** Controller → Service → Model. No query logic in Blade or Controllers. One partial = one section. Services return typed DTOs/arrays, not Eloquent models, to the view.
- **Security:** no secrets in Blade or JS; media URLs from config; UGC sanitized and moderated before publish.
- **SEO:** breadcrumb + `TouristDestination` / `TouristAttraction` JSON-LD on city and interest pages, sitemap entries, hreflang per locale (`docs/SEO.md` rules).
- **Accessibility:** video muted + captions, focus-visible states, keyboard-reachable map alternatives (the city list *is* the accessible fallback), AA contrast on every new palette pairing.
- **Testing:** feature test per new route (200 + content), service unit tests, no regression on booking/checkout suites.

---

## 6. Decisions needed before implementation starts

1. **Hero video** — who supplies it? (Doc recommends real stock footage + cinematic edit; AI only for transitions/enhancement.) Without it, Phase 1 ships with a high-quality image-sequence fallback.
2. **Cities** — new `cities` table with `city_id` FK on experiences (clean, needs a backfill migration), or keep the current `location_city` string and layer content on top (faster, messier)? **Recommendation: new table + backfill.**
3. **Map** — Leaflet with styled GeoJSON regions (already a dependency, pragmatic) vs. a custom hand-drawn SVG Morocco map (more premium, more design work). **Recommendation: custom SVG regions rendered through Leaflet's overlay so we get both.**
4. **Content ownership** — who writes the city/interest/food/craft copy in 3 languages? This is the critical path for Phases 3–4, not the code.
5. **Phase 1 vs Phase 5 order** — do we publish "Verified Agencies" claims on the landing page before the verification workflow exists? **Recommendation: no — ship the schema + admin flow with Phase 1.**
6. **Scope of the first delivery** — Phase 0+1 only (landing page), or Phase 0+1+2 (landing + discover hub) as one release?

---

## 7. Suggested first sprint (once approved)

```
Phase 0  Design foundations                       ~2–3 days
Phase 1  Landing page 7 sections                  ~5–7 days
Phase 5a Agency verification schema + admin flow  ~2 days   (parallel)
──────────────────────────────────────────────────────────
Result: the new front door is live, trust claims are real, /discover is stubbed
        with the 6 interests and a "coming soon" hub. Booking engine untouched.
```
