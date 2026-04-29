# 07 — Roadmap

Phased plan. Each phase ends with something demoable.

---

## Phase 1 — Backend foundation

**Goal:** WordPress is running locally and serving structured content over the REST API.

### Deliverables
- Docker Compose stack up (`docker compose up -d`)
- Custom plugin scaffold: `wp-content/plugins/northium-cms/`
- 5 CPTs registered: Division, Service, Advisor, Article, Campaign
- 2 taxonomies registered: `article_category`, `service_tag`
- Default WP REST endpoints respond for each CPT
- Seed data: 5 divisions, ~10 services, ~5 advisors, ~5 articles
- A minimal admin-only theme so WP doesn't error on missing templates

### Done when
- `curl http://localhost:8080/wp-json/wp/v2/division` returns 5 entries
- An editor can create all 5 entity types from wp-admin without errors

---

## Phase 2 — Custom REST endpoints

**Goal:** The frontend has the composed endpoints it needs to render pages without waterfalls.

### Deliverables
- `northium/v1/divisions` — index list
- `northium/v1/division/{slug}` — full hub response
- `northium/v1/service/{slug}` — service + parent division + related
- `northium/v1/insights` — paginated, filterable
- `northium/v1/insights/{slug}` — article + author + related
- `northium/v1/campaign/{slug}` — campaign + target division
- `northium/v1/search` — cross-CPT search

### Done when
- Each endpoint returns the shape documented in `04-api.md`
- Each endpoint has an HTTP status code test (200/404/400)
- Endpoints set sensible cache headers

---

## Phase 3 — Frontend foundation

**Goal:** Next.js is up, fetching real WP data, and rendering the homepage and divisions index.

### Deliverables
- `web/lib/api.ts` typed client
- `web/lib/types.ts` content types matching API responses
- Header + Footer + base layout
- Token-based CSS in `globals.css`
- Homepage with hero + divisions grid + insights preview
- `/divisions` index page

### Done when
- `npm run dev` renders the homepage with live WP data
- Lighthouse runs cleanly (no console errors, no broken images)

---

## Phase 4 — Division hubs and detail pages

**Goal:** Every CPT has a frontend page wired to its API endpoint.

### Deliverables
- `/divisions/{slug}` — division hub (the flagship page)
- `/services/{slug}` — service detail
- `/advisors`, `/advisors/{slug}` — team pages
- `/insights`, `/insights/{slug}` — articles
- `generateStaticParams` for known slugs
- `generateMetadata` on all pages

### Done when
- All 5 division hubs render correctly
- Navigating across the site never 404s on valid slugs
- Page transitions feel instant (cached HTML)

---

## Phase 5 — Flagship features

**Goal:** Ship the three features in `06-features.md`.

### Deliverables
- Division filter on `/insights` (URL-driven)
- Campaign pages at `/campaigns/{slug}` with section repeater
- `/search` page with cross-CPT search
- Revalidate webhook from WP → Next.js

### Done when
- A campaign created in admin appears on the frontend within 1 minute
- Filtering insights by division works end-to-end with URL updates
- Search returns grouped results across all 4 CPTs

---

## Phase 6 — Polish & ship-ready

**Goal:** The project looks and feels like a real product.

### Deliverables
- Loading states for every async page
- Error boundaries with branded fallbacks
- 404 page
- Mobile responsive QA across all routes
- SEO sitemap at `/sitemap.xml`
- JSON-LD per page type
- README.md at repo root with setup, screenshots, demo link
- Deployment notes (Vercel + WP host)

### Done when
- Lighthouse scores: Performance ≥ 90, Accessibility ≥ 95, SEO ≥ 95
- README looks professional enough to share in a job application
- A stranger can clone, install, and run locally in under 10 minutes

---

## Phase 7 (optional) — Stretch goals

Pick from these only if Phases 1–6 are solid:

- French (FR-CA) localization
- Editor preview mode (draft posts visible from Next.js)
- Headless WP authentication for a small client portal
- Algolia or Meilisearch instead of WP search
- Image CDN with on-the-fly resizing

---

## Estimated effort

| Phase | Rough effort |
|---|---|
| 1 — Backend foundation | 1–2 days |
| 2 — Custom REST endpoints | 1–2 days |
| 3 — Frontend foundation | 1 day |
| 4 — Detail pages | 2–3 days |
| 5 — Flagship features | 2–3 days |
| 6 — Polish | 1–2 days |
| **Total** | **~8–13 days of focused work** |

This is a portfolio-grade project that demonstrates real-world judgment: the content model, the API design, the rendering strategy, and the SEO/perf discipline are all defensible in a technical interview.
