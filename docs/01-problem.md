# 01 — Problem & Context

## The firm

**Northium Financial** is an independent, fee-only advisory firm. It was started by ex-bank advisors who got tired of opaque fees and product-pushing, and built around five distinct practices instead of the conventional "wealth + insurance + everything else" lineup.

### The five practices

| Practice | What they do |
|---|---|
| **Wealth Planning** | Fee-only investment and retirement planning. No kickbacks, no in-house funds. |
| **Tax & Estate** | Year-round tax work and estate planning, not just April fire drills |
| **Family CFO** | Bill management, cash-flow tracking, and household finance for complex households |
| **Founder & Equity** | Stock comp, RSUs, options, secondary sales, and exit planning for founders and executives |
| **Studio** | Northium's research desk — published, anonymized briefs from real client work |

Each practice has its own:
- audience (mid-career professionals, dual-income households, founders, complex-household principals)
- service catalog (productized, named, scoped)
- lead advisor
- editorial cadence (briefs published by the practice, not by the firm at large)

---

## The real problem

A coupled WordPress theme — one site, one theme, one giant menu — does not fit a multi-practice firm cleanly. Specifically:

### 1. Multi-practice content is structurally complex
Content has to be organized **by practice**, not just by category. A "Service" only makes sense in the context of the practice it belongs to. An "Advisor" leads inside a specific practice. A "Brief" targets a specific audience.

A flat blog + Pages model collapses this structure and forces editors to fake it with categories and naming conventions.

### 2. Marketing and content need to scale
Northium publishes campaigns, landing pages, and briefs across practices. They need:
- **content reuse** (one service summary feeding multiple practice pages)
- **fast updates** (publishing without waiting on a developer)
- **SEO control** (per-page metadata, structured data, fast load)
- **multi-channel publishing** (web today, possibly newsletter or partner site tomorrow)

A theme-locked monolith makes all of this slow.

### 3. Brand register has to read as "next-gen advisory", not "another bank"
Northium's positioning depends on *not* looking like the institutions it's distinguishing itself from. Page builders and stock financial-services themes hit a ceiling on:
- typography control
- motion and interaction polish
- bespoke section layouts
- performance scores

Looking like a bank undercuts the whole "without the bank" pitch.

### 4. Performance and SEO
Modern WordPress stacks struggle with Core Web Vitals out of the box: heavy themes, render-blocking assets, slow time-to-first-byte. The frontend needs to ship lean, cached, and statically rendered where possible.

---

## Why headless solves it

Splitting WordPress (content) from Next.js (presentation) directly addresses all four pain points:

| Pain | How headless fixes it |
|---|---|
| Multi-practice structure | Custom Post Types + a `Division` taxonomy/relationship model* |
| Scale & reuse | Content is queried by API; any surface can consume it |
| Brand register | Frontend is a real codebase, not a theme |
| Performance & SEO | Next.js renders static / streamed HTML, controls metadata fully |

WordPress stays as the editor's tool — familiar, fast for content ops. Next.js becomes the product surface.

*\* In the code, the CPT slug is `division`. On the public site the user-facing label is "practice". The internal name didn't change during the rebrand for code stability.*

---

## Non-goals

To keep scope honest, this project is **not**:

- a full e-commerce build
- a client portal (auth, account dashboards)
- a CRM replacement
- a multilingual / multi-region rollout (defer to v2)

The goal is a **content + marketing platform** that proves the headless multi-practice pattern end to end.
