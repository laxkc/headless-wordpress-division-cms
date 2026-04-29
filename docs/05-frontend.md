# 05 — Frontend (Next.js)

## App Router structure

```
web/app/
├── layout.tsx                       Root layout (header, footer, fonts)
├── page.tsx                         Homepage
├── globals.css                      Tokens + base styles
│
├── divisions/
│   ├── page.tsx                     /divisions — index of 5 divisions
│   └── [slug]/
│       └── page.tsx                 /divisions/{slug} — division hub
│
├── services/
│   └── [slug]/
│       └── page.tsx                 /services/{slug} — service detail
│
├── advisors/
│   ├── page.tsx                     /advisors — team listing
│   └── [slug]/
│       └── page.tsx                 /advisors/{slug} — advisor profile
│
├── insights/
│   ├── page.tsx                     /insights — paginated, filterable
│   └── [slug]/
│       └── page.tsx                 /insights/{slug} — article detail
│
├── campaigns/
│   └── [slug]/
│       └── page.tsx                 /campaigns/{slug} — landing page
│
├── search/
│   └── page.tsx                     /search?q=...
│
└── api/
    └── revalidate/
        └── route.ts                 Webhook from WordPress
```

---

## Page-by-page composition

### `/` — Homepage
Sections, top to bottom:
1. **Hero** — "Financial advice without the bank.", subline, primary CTA
2. **Practices grid** — 5 cards, each links to `/divisions/{slug}`
3. **Featured services** — 3–6 hand-picked services across practices
4. **Latest briefs** — last 3 articles
5. **Advisors preview** — 4 advisor cards
6. **Footer CTA** — generic contact prompt

Data: `northium/v1/divisions` + `northium/v1/insights?per_page=3` + `northium/v1/advisors`

### `/divisions` — Practices index
Just the 5 practices as large cards. No filtering needed.

Data: `northium/v1/divisions`

### `/divisions/{slug}` — Practice hub (the killer page)
1. **Hero** — practice-specific intro
2. **About this practice** — `full_description`
3. **Services in this practice** — grid
4. **Advisors in this practice** — grid
5. **Briefs from this practice** — last 3 articles
6. **Active campaigns** (if any)
7. **CTA** — "Talk to a {practice} advisor"

Data: a single call to `northium/v1/division/{slug}` (avoids 4 waterfall fetches).

### `/services/{slug}` — Service detail
1. Breadcrumb: Home › Divisions › {Division} › {Service}
2. Hero with service image
3. Body / details
4. Related articles
5. CTA

Data: `northium/v1/service/{slug}`

### `/advisors` and `/advisors/{slug}`
Standard listing + profile pages. Profile shows practices served and a contact prompt.

### `/insights` — Articles index
- Filter chips: All practices | Wealth Planning | Tax & Estate | Family CFO | Founder & Equity | Studio
- Optional category dropdown
- Paginated grid (12 per page)

Data: `northium/v1/insights?division={slug}&page={n}`

### `/insights/{slug}` — Article detail
Article body, author byline, related articles by division.

### `/campaigns/{slug}` — Campaign landing page
Fully composed from the campaign's `sections` repeater. Styled with the target division's accent color.

### `/search` — Cross-CPT search
Single input, results grouped by content type.

---

## Rendering strategy

| Route | Strategy | Why |
|---|---|---|
| `/` | Static + ISR (5 min) | Stable hero, occasional insights changes |
| `/divisions` | Static + ISR (10 min) | Rarely changes |
| `/divisions/{slug}` | Static + ISR (5 min) | Most-trafficked detail pages |
| `/services/{slug}` | Static + ISR (10 min) | |
| `/advisors`, `/advisors/{slug}` | Static + ISR (1 hour) | Low change rate |
| `/insights` | Dynamic (`force-dynamic`) when filters set, ISR when bare | Filter combos explode the static surface |
| `/insights/{slug}` | Static + ISR (1 hour) | |
| `/campaigns/{slug}` | Static + ISR (5 min) | Marketing wants fast iteration |
| `/search` | Dynamic | Always query-driven |

`generateStaticParams` pre-builds known slugs for divisions, services, advisors. New entries appear on first visit via ISR.

---

## SEO

- `generateMetadata` on every page sets title, description, canonical, OpenGraph, Twitter Card from CMS data.
- JSON-LD per page type:
  - Homepage → `Organization`
  - Division → `Service` (or `FinancialProduct` where relevant)
  - Article → `Article`
  - Advisor → `Person`
- Sitemap generated at `/sitemap.xml` from CMS data.
- `robots.txt` allows crawling everywhere except `/api/`.

---

## Component library (proposed)

```
web/components/
├── layout/        Header, Footer, Container
├── division/      DivisionCard, DivisionHero, DivisionGrid
├── service/       ServiceCard, ServiceGrid
├── advisor/       AdvisorCard, AdvisorGrid, AdvisorBio
├── article/       ArticleCard, ArticleList, ArticleBody
├── campaign/      CampaignHero, CampaignSection
├── ui/            Button, Badge, Breadcrumb, Pagination, FilterChips
└── seo/           JsonLd
```

Co-locate styles with components using CSS modules. Tokens live in `globals.css`.

---

## Data fetching pattern

All API calls go through a typed client at `web/lib/api.ts`:

```ts
export async function getDivision(slug: string) { ... }
export async function getDivisions() { ... }
export async function getService(slug: string) { ... }
export async function getInsights(params: InsightsParams) { ... }
```

Server Components call these directly. Client Components only need them for interactive surfaces (search, filters), where they hit Route Handlers under `/api/` that proxy to WP.
