# 02 — Architecture

## High-level diagram

```
┌──────────────┐      ┌────────────────────┐      ┌─────────────────────┐
│   Visitor    │ ───▶ │  Next.js (web/)    │ ───▶ │  WordPress REST API │
│  (browser)   │      │  App Router · SSR  │      │  /wp-json/...       │
└──────────────┘      └────────────────────┘      └──────────┬──────────┘
                                                              │
                                                              ▼
                                                   ┌─────────────────────┐
                                                   │  WordPress Core     │
                                                   │  + northium-cms     │
                                                   │     plugin          │
                                                   └──────────┬──────────┘
                                                              │
                                                              ▼
                                                   ┌─────────────────────┐
                                                   │     MySQL / DB      │
                                                   └─────────────────────┘
```

**Visitor** never talks to WordPress directly. The Next.js app is the only public surface; WordPress sits behind it as an internal service.

---

## Layer responsibilities

| Layer | Owns | Does NOT own |
|---|---|---|
| **Next.js (`web/`)** | Routing, layout, SEO metadata, caching strategy, client interactions | Content, business taxonomy, editor UX |
| **WordPress Core** | Auth for editors, media library, revisions, scheduling, the admin UI | Public-facing rendering, theme polish |
| **`northium-cms` plugin** | Custom Post Types, taxonomies, REST endpoints, ACF/meta-box config | Generic CMS plumbing (delegates to core) |
| **Theme (admin-only)** | A minimal stub theme so WP runs cleanly | Public templates — there are none |

---

## Data flow

### Read path (most traffic)
1. Visitor requests `/divisions/founder-and-equity`
2. Next.js page component calls `fetch('/wp-json/northium/v1/division/founder-and-equity')`
3. WordPress plugin assembles the response: division + related services + advisors + articles
4. Next.js renders HTML, caches the result
5. Subsequent requests hit the Next.js cache (ISR or `fetch` cache), not WordPress

### Write path (editor traffic)
1. Editor logs into `/wp-admin`
2. Creates/edits a Service, picks its Division, hits Publish
3. Optional revalidate webhook hits Next.js to invalidate cache for affected paths

---

## Tech stack

### Backend
- **WordPress** (latest stable) — content store, editor UI
- **PHP 8.3**
- **MySQL** (or PostgreSQL via pg4wp, matching existing repo convention)
- Custom plugin: `northium-cms`
- Optional: ACF (Advanced Custom Fields) for meta UI

### Frontend
- **Next.js 15** (App Router)
- **React 19**
- **TypeScript**
- Styling: vanilla CSS modules + tokens (no Tailwind, matching existing repo convention)
- Image optimization: `next/image` pointed at the WP media domain

### Infra (dev)
- **Docker Compose** for the WP + DB + Adminer stack
- Next.js runs on host (`npm run dev`) against `http://localhost:8080` for the API

### Infra (prod, future)
- WordPress on a small VPS or managed WP host
- Next.js on Vercel (or any Node host)
- A CDN in front of the Next.js app

---

## Why this split (vs. alternatives)

| Alternative | Why we didn't pick it |
|---|---|
| **Pure WordPress + theme** | Fails the brand-polish, performance, and structure problems from doc 01 |
| **Next.js + a markdown repo** | Editors are not developers; non-tech staff need an admin UI |
| **Next.js + Sanity / Contentful** | Real, but adds vendor lock-in and a new tool for editors to learn. WordPress is already familiar to financial-services marketing teams. |
| **WordPress + JAM-stack export (e.g. WP2Static)** | Loses dynamic features (search, division filtering with fresh data) |

Headless WordPress hits the sweet spot: a **familiar editor experience** plus a **modern frontend**.

---

## Security & boundaries

- WordPress admin is **not** publicly linked from the Next.js site
- REST endpoints are **read-only and public** for content; write operations require WP auth
- No secrets in the frontend — all server-side fetches happen in Server Components or Route Handlers
- CORS is locked to the Next.js origin in production
