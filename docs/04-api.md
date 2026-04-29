# 04 — REST API

The plugin exposes content through two surfaces:

1. **Default WP REST API** — auto-generated for each CPT (`show_in_rest: true`)
2. **Custom `northium/v1` namespace** — composed responses tailored for the frontend

The frontend should prefer custom endpoints whenever a page needs cross-CPT data, to avoid waterfall fetches.

---

## Default WP endpoints

| Endpoint | Returns |
|---|---|
| `GET /wp-json/wp/v2/division` | List of all divisions |
| `GET /wp-json/wp/v2/division/{id}` | Single division by ID |
| `GET /wp-json/wp/v2/service` | List of all services |
| `GET /wp-json/wp/v2/service?division={id}` | Services filtered by division |
| `GET /wp-json/wp/v2/advisor` | List of all advisors |
| `GET /wp-json/wp/v2/article` | List of all articles |
| `GET /wp-json/wp/v2/campaign` | List of all campaigns |

These exist for free once each CPT registers with `show_in_rest: true`. Useful for the admin / quick lookups; not ideal for page composition.

---

## Custom `northium/v1` endpoints

### `GET /wp-json/northium/v1/divisions`
Compact list for the practices index page.

```json
{
  "items": [
    {
      "slug": "wealth-planning",
      "title": "Wealth Planning",
      "short_description": "Fee-only investment and retirement planning. No kickbacks, no in-house funds.",
      "hero_image": "https://.../wealth.jpg",
      "accent_color": "#1a365d"
    }
  ]
}
```

### `GET /wp-json/northium/v1/division/{slug}`
The hub response — practice + everything related. This is the main feature endpoint.

```json
{
  "division": {
    "slug": "founder-and-equity",
    "title": "Founder & Equity",
    "short_description": "Stock comp, RSUs, options, secondary sales, and exit planning for founders and executives.",
    "full_description": "<p>...</p>",
    "hero_image": "https://.../hero.jpg",
    "accent_color": "#4a3d7e"
  },
  "services": [
    {
      "slug": "rsu-options-planning",
      "title": "RSU & Options Planning",
      "summary": "Vesting calendars, 10b5-1 plans, exercise strategy, AMT modelling.",
      "icon": null
    }
  ],
  "advisors": [
    {
      "slug": "marcus-reyes",
      "name": "Marcus Reyes",
      "role": "Founder Services Lead",
      "profile_image": null
    }
  ],
  "articles": [
    {
      "slug": "three-rsu-mistakes",
      "title": "The three RSU mistakes we see most often",
      "publish_date": "2026-04-12",
      "featured_image": null
    }
  ],
  "campaigns": []
}
```

### `GET /wp-json/northium/v1/service/{slug}`
Service detail + its parent division + related articles.

```json
{
  "service": { "...": "..." },
  "division": { "slug": "...", "title": "..." },
  "related_articles": [ "..." ]
}
```

### `GET /wp-json/northium/v1/insights`
Paginated briefs list with practice/category filters.

Query params:
- `division` (slug) — filter to one practice (e.g. `founder-and-equity`)
- `category` (slug) — filter to one editorial category
- `page` (int, default 1)
- `per_page` (int, default 12)

```json
{
  "items": [ "..." ],
  "page": 1,
  "per_page": 12,
  "total": 47,
  "total_pages": 4
}
```

### `GET /wp-json/northium/v1/insights/{slug}`
Single brief + author + related briefs in the same practice.

### `GET /wp-json/northium/v1/campaign/{slug}`
Single campaign page assembled with target practice and resolved sections.

### `GET /wp-json/northium/v1/search?q={query}`
Cross-CPT search across practices, services, advisors, briefs.

```json
{
  "query": "RSU",
  "results": {
    "divisions": [ "..." ],
    "services":  [ "..." ],
    "advisors":  [ "..." ],
    "articles":  [ "..." ]
  }
}
```

---

## Implementation notes

- All custom endpoints register through `register_rest_route( 'northium/v1', ... )` in the plugin's `includes/rest/` folder.
- Each endpoint has a dedicated controller class — no fat closures.
- Image URLs are resolved server-side; clients never construct paths.
- Endpoints set sensible cache headers (`Cache-Control: public, max-age=300`).
- Errors return `WP_Error` with proper HTTP status codes (`404` for missing slug, `400` for bad params).

---

## Caching strategy

| Layer | TTL | Invalidation |
|---|---|---|
| WordPress object cache | request-scoped | per-request |
| Next.js `fetch` cache | 5 min default | manual `revalidatePath` on publish |
| CDN | 1 hour | webhook from WP `save_post` |

A revalidation webhook (`POST /api/revalidate`) on the Next.js app receives a signed payload from WP on publish/update events and invalidates the affected paths.

---

## Authentication

The public read endpoints listed above require **no auth**. Only write operations (used by editors via wp-admin) hit authenticated endpoints, and those go through WordPress's standard cookie/nonce flow — the Next.js frontend never writes.
