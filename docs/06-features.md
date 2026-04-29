# 06 — Features

The strongest features are the ones that map directly onto Northium's actual business. This doc focuses on the three flagship features that justify the headless architecture.

---

## Feature 1 — Practice-based content filtering (flagship)

**The pitch:** Pick a practice and the entire site narrows to that practice's services, advisors, briefs, and campaigns.

### User flow
1. Visitor lands on `/divisions`
2. Selects, e.g., **Founder & Equity**
3. Lands on `/divisions/founder-and-equity` — a hub showing only that practice's content
4. Filters on `/insights` retain the practice selection: `/insights?division=founder-and-equity`
5. CTAs across the site link to practice-specific contact paths

### Why this matters
Northium's audiences are **mutually exclusive** — a tech founder thinking about an upcoming liquidity event doesn't want to wade through household-CFO content. Practice filtering respects that.

### Implementation
- One API call per hub page: `GET /northium/v1/division/{slug}` returns the practice and all related content in one response.
- Filter chips on `/insights` are URL-driven (so they're shareable and SEO-friendly).
- Practice accent color is threaded through hero/CTAs for visual continuity.

### Acceptance criteria
- [ ] All 5 practice hub pages render with services, advisors, briefs
- [ ] Insights page filters update the URL and the result set
- [ ] Empty practices render a graceful "no content yet" state, not a crash
- [ ] Each practice page passes Lighthouse SEO ≥ 95 and LCP < 2.5s

---

## Feature 2 — Campaign landing pages

**The pitch:** Marketing can spin up a fully-branded landing page for a campaign without touching a developer.

### User flow
1. Marketing creates a `Campaign` in WP admin
2. Sets target practice, hero copy, CTA, and an arbitrary number of `sections`
3. Page is live at `/campaigns/{slug}`
4. Inherits the target practice's accent color and "back to {practice}" link

### Why this matters
Northium runs campaigns tied to specific practices (the seed data ships a *Year-End Tax Review 2026* campaign for Tax & Estate). Without this, every new campaign goes through dev. With this, marketing iterates on its own.

### Implementation
- `Campaign` CPT with a `sections` repeater (heading + body + image)
- Frontend renders sections in order; each section is a layout-flexible component
- Campaign pages set their own meta tags (title, OG image) for paid ads

### Acceptance criteria
- [ ] A campaign created in WP admin appears at `/campaigns/{slug}` within 1 minute (via revalidate webhook)
- [ ] The page picks up the target practice's accent color
- [ ] Sections support text-left/text-right/full-bleed variants
- [ ] OG image and meta description are settable per campaign

---

## Feature 3 — Cross-CPT search

**The pitch:** A single search box that looks across practices, services, advisors, and briefs.

### User flow
1. Visitor types in the header search field
2. Hits `/search?q=RSU`
3. Results are grouped by type, with the most relevant types first

### Why this matters
A user searching "RSU" might want a service, an advisor, or a brief — and they don't know the difference. Grouping by type lets them self-select.

### Implementation
- Single endpoint `GET /northium/v1/search?q={query}` runs WP search across the 4 CPTs
- Results page is a Server Component reading the query string
- Snippet highlighting on the matched term (light, server-side)

### Acceptance criteria
- [ ] Search returns results across all 4 content types
- [ ] Empty queries show a helpful prompt, not an empty page
- [ ] Misspelled queries fall back to a fuzzy match (stretch goal)

---

## Feature ranking

If only one ships, **Feature 1 (practice filtering)** is non-negotiable — it's what makes this project specifically *Northium-shaped* rather than a generic headless demo.

Feature 2 is the strongest "marketing team will love this" win.
Feature 3 is the lowest-cost polish that rounds out the experience.

---

## Out of scope (for v1)

These are tempting but defer:

- **Personalization** — showing returning visitors their last-viewed practice
- **A/B testing infra** — campaign variants
- **Booking / scheduling** — would require a real calendar integration
- **Newsletter signup** — easy to add later, but needs ESP integration choices
- **Multi-language** — FR-CA support would be a real v2
- **Pricing / how-we-work pages** — separate Level 3 refactor (see implementation plan)
