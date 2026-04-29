# 03 — Content Model

The content model is the spine of this project. Get it right and everything else (API, routes, filtering) falls out naturally.

## Custom Post Types

| CPT | Slug | Purpose |
|---|---|---|
| Division | `division` | The 5 business units — the organizing axis of the whole site |
| Service | `service` | An offering inside a division |
| Advisor | `advisor` | A team member tied to one or more divisions |
| Article | `article` | Blog/insights post, optionally tied to a division |
| Campaign | `campaign` | Marketing landing page targeting one division |

All CPTs are `public: true`, `show_in_rest: true`, with custom `rewrite` slugs.

---

## Field definitions

### Division
The root entity. Everything else relates back to a Division.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | string | yes | "Wealth Planning" |
| `slug` | string | yes | URL fragment, e.g. `wealth-planning` |
| `short_description` | text | yes | One-line summary for cards/listings |
| `full_description` | rich text | yes | Long-form intro for the division page |
| `hero_image` | image | yes | Top-of-page hero |
| `accent_color` | string (hex) | no | Per-division brand accent (optional) |
| `featured_services` | relation → Service[] | no | Curated picks for the division page |
| `featured_advisors` | relation → Advisor[] | no | Curated picks for the division page |
| `order` | int | no | Manual sort within the divisions grid |

### Service
| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | string | yes | "Estate Planning" |
| `slug` | string | yes | |
| `division` | relation → Division | yes | The owning division |
| `summary` | text | yes | 1–2 sentence card description |
| `body` | rich text | yes | Full service detail page |
| `icon` | image / svg | no | Used in service cards |
| `featured_image` | image | no | Header image on the detail page |
| `cta_label` | string | no | e.g. "Book a consultation" |
| `cta_url` | string | no | |

### Advisor
| Field | Type | Required | Notes |
|---|---|---|---|
| `name` | string | yes | Stored in `title` |
| `role` | string | yes | "Senior Wealth Advisor" |
| `divisions` | relation → Division[] | yes | An advisor may serve multiple |
| `bio` | rich text | yes | |
| `profile_image` | image | yes | |
| `email` | email | no | |
| `linkedin_url` | url | no | |

### Article
| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | string | yes | |
| `content` | rich text | yes | |
| `category` | taxonomy term | no | Free-form taxonomy `article_category` |
| `division` | relation → Division | no | Optional; some articles are firm-wide |
| `featured_image` | image | yes | |
| `author` | relation → Advisor or User | no | |
| `publish_date` | date | yes | Standard WP post date |

### Campaign
A marketing landing page — strong differentiator from generic "Page" content.

| Field | Type | Required | Notes |
|---|---|---|---|
| `campaign_title` | string | yes | |
| `target_division` | relation → Division | yes | Drives styling & related content |
| `hero_headline` | string | yes | |
| `hero_subtext` | text | yes | |
| `cta_text` | string | yes | |
| `cta_url` | string | yes | |
| `sections` | repeater | yes | Array of `{ heading, body, image }` blocks |
| `testimonials` | repeater | no | `{ quote, attribution, role }` |

---

## Taxonomies

| Taxonomy | Attached to | Purpose |
|---|---|---|
| `article_category` | Article | Editorial categories (Insights, News, Press) |
| `service_tag` | Service | Cross-cutting tags (Retirement, Tax, Group, etc.) |

Note: **Division is modelled as a CPT, not a taxonomy.** This is deliberate — divisions need their own page, hero image, featured content, and lifecycle, which a taxonomy term cannot cleanly hold.

---

## Relationship diagram

```
                     ┌─────────────┐
                     │  Division   │ ◀── target_division ──┐
                     └──────┬──────┘                       │
                            │                              │
        ┌───────────────────┼───────────────────┐          │
        │                   │                   │          │
        ▼                   ▼                   ▼          │
   ┌─────────┐         ┌─────────┐         ┌─────────┐  ┌──┴────────┐
   │ Service │         │ Advisor │         │ Article │  │ Campaign  │
   └─────────┘         └─────────┘         └─────────┘  └───────────┘
                                            (optional)
```

Every leaf entity points back to a Division. That's the relationship the API and the frontend lean on.

---

## Naming convention

- CPT slugs: singular (`division`, `service`, `advisor`, `article`, `campaign`)
- REST base: same as CPT slug
- Taxonomy slugs: snake_case (`article_category`, `service_tag`)
- Field keys (meta): snake_case (`hero_image`, `featured_services`)
