# Webstar CORE — agent invariants

**Last verified:** 2026-11-15 — run `php bin/agent-context-validate.php` after shipping articles.

## Business and voice

- **Operator:** Ian McDonnell, Webstar Business Services LLC.
- **Audience:** Michigan small business owners choosing or launching a website they own.
- **Content:** People-first ([CONTENT-SEO.md](../CONTENT-SEO.md)); no fake clients, metrics, or rankings guarantees.
- **Case studies:** Client approval and real numbers only.

## Locked NAP (public — use identically everywhere)

```text
Webstar Business Services · Metro Detroit & surrounding Michigan communities · 248-564-3663 · https://www.webstarbusinessservices.com · ian@webstarbusinessservices.com
```

- Never list a personal cell on public listings.
- Source: [SEO.md](../../SEO.md). Site phone: `library/site-config.php` (`phone_display` / `phone_tel`).

## Website packages (one-time — source: `library/packages-data.php`)

| Slug | Price | Pages | Booking | Integrations |
|------|-------|-------|---------|--------------|
| `simple-website` | $350–$500 | 3 | Call/book buttons only | — |
| `business-website` | $900–$1,800 | 5–10 | One booking/integration included | 1 third-party |
| `business-growth-website` | $2,500+ (quoted) | Unlimited (scoped) | Custom flows | Unlimited as quoted |

Every tier includes Google Business Profile, Analytics 4, and Search Console setup.

## Monthly offers (`library/plans-data.php`)

- **Website as a Service:** $150/mo — export ownership after month 6; see cost guide.
- **Care Plan:** $50/mo — hosting, backups, edit allowance after outright buy.

## Live Resources guides (10)

Canonical list — metadata in `library/articles-data.php`, body in `library/articles/{slug}.php`:

1. `website-cost-small-business-michigan-2026`
2. `website-as-a-service-vs-buy-outright`
3. `google-business-profile-checklist-metro-detroit`
4. `what-to-put-on-3-page-website`
5. `wix-squarespace-vs-custom-website`
6. `website-launch-checklist-domain-hosting`
7. `first-10-google-reviews-small-business`
8. `businesses-in-chatgpt-local-ai-search`
9. `low-and-slow-seo-geo-2026`
10. `choosing-website-features`

**Not live yet (proposed):** Square ecommerce guide, low-cost custom 2026, MDHL / Gypsie Locs / DJ's Tackle case studies — see [resource-topics.csv](../content-revamp/resource-topics.csv).

## Content and SEO revamp status

- **Landings:** 32 total — 10 in `seo-pages-data.php` (light edit), 22 in `seo-pages-batch.php` (heavy rewrite). Disposition: [phase0-summary.md](../content-revamp/phase0-summary.md).
- **Keyword map:** One winning URL per head term — [SEO.md](../../SEO.md). Do not create competing URLs.
- **Article internals:** Use `webstar_url()` and `webstar_h()` in article PHP bodies for links.

## Path A clients (in this repo)

| Slug | Notes |
|------|--------|
| `metro-detroit-holiday-lighting` | Staging; live domain separate |
| `superior-ice-adventures` | Staging subdomain |
| `djstackle` | Square shop via `clients/shared/shop/`; `shop-config.php` gitignored |
| `gypsie-locs-hair-studio` | Design draft; GlossGenius booking; live domain TBD |

Staging under `/clients/` — **disallowed in robots.txt** (not for public SEO). See [clients/README.md](../../clients/README.md).

**Local base:** `http://localhost/webstar-business-solutions/clients/{slug}/`

## Never commit

From [.gitignore](../../.gitignore): `library/mail-config.php`, `library/calendar-config.php`, `clients/*/includes/shop-config.php`, `*.local.php`, `lead-gen/data/`, client sqlite, `.env`.

`library/portal-clients.php` may contain bcrypt hashes only — never plaintext passwords.

## Agent hard constraints

- **Commits:** Only when the user explicitly asks.
- **Scope:** Minimal diff; do not edit unrelated clients or batch landings.
- **PHP:** `declare(strict_types=1);` in library code; escape output with `webstar_h()`.
- **Article dates:** Stagger honest `published` / `updated` in `articles-data.php` on ship — no backdating.
- **Shell:** PowerShell on Windows — use `;` not `&&`.
- **Explore:** No full read of `seo-pages-batch.php`, `vendor/`, or `shared/shop/vendor/` without a named slug.