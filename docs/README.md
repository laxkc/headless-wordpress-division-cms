# Northium — Headless Multi-Practice CMS

> Project documentation for a headless WordPress + Next.js platform built around Northium Financial's multi-practice business model.

This folder contains the design, scope, and build plan. Code lives in:

- `wp-content/plugins/northium-cms/` — custom WordPress plugin (CPTs, REST endpoints)
- `wp-content/themes/` — minimal admin-only theme
- `web/` — Next.js 16 App Router frontend
- `docker/` — local dev stack (MySQL + WordPress + Adminer + WP-CLI)

---

## Documentation Index

| # | Doc | What it covers |
|---|---|---|
| 01 | [Problem & Context](./01-problem.md) | Who Northium is, the real problem, why headless |
| 02 | [Architecture](./02-architecture.md) | System diagram, data flow, tech stack |
| 03 | [Content Model](./03-content-model.md) | Custom Post Types, fields, taxonomies, relationships |
| 04 | [REST API](./04-api.md) | Default endpoints + custom `northium/v1` routes |
| 05 | [Frontend](./05-frontend.md) | Next.js App Router routes and page composition |
| 06 | [Features](./06-features.md) | Practice-based filtering, campaign pages, content hub |
| 07 | [Roadmap](./07-roadmap.md) | Phased build plan with deliverables per phase |
| 08 | [Implementation Plan](./08-implementation-plan.md) | First-principles plan: decisions, steps, verification gates, risks |

---

## Project at a glance

- **Brand**: Northium Financial — independent, fee-only, plain-language
- **Practices**: Wealth Planning · Tax & Estate · Family CFO · Founder & Equity · Studio
- **Problem**: Multi-practice content is too complex for a coupled WordPress theme
- **Solution**: Headless — WordPress owns content, Next.js owns presentation
- **Killer feature**: Practice-based content filtering with a custom REST endpoint
- **Repo target**: `headless-wordpress-multi-practice-cms`

---

## How to read these docs

Read in order on a first pass (01 → 08). After that, jump to the doc that matches the layer you're working on:

- Backend / WordPress work → `03-content-model.md`, `04-api.md`
- Frontend / Next.js work → `05-frontend.md`, `06-features.md`
- Planning a sprint → `07-roadmap.md`

---

## Note on terminology

In the docs and the code, **"division"** is the technical term for the CPT (`division`, REST `/division`, post-meta `division_id`). On the public-facing site the user-visible label is **"practice"** — Northium positions these as professional-services practices, not corporate divisions. The data model name stayed for code stability; only the UI label changed.
