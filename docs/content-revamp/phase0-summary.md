# Phase 0 summary — content revamp

**Generated:** 2026-11-12  
**Inventory:** [phase0-inventory.csv](phase0-inventory.csv) (39 URLs — 7 guides + 32 landings)  
**Tool:** `bin/content-phase0-audit.php`

## Disposition totals

| Disposition | Count | Notes |
|-------------|------:|-------|
| **rewrite** | 29 | All 7 guides + all 22 `seo-pages-batch.php` landings |
| **keep** | 10 | All `seo-pages-data.php` landings (light edit: meta, metro in industry titles, trim “search engine” phrasing) |
| **merge** | 0 | No duplicate head terms; every slug owns one row in SEO.md |

**Scaled-content risk:** concentrated in **batch file** (~700–755 words/page, max section **193–452 words**). Data file pages ~108–254 words total.

## Guides (7) — all **rewrite** (P1)

| Slug | Words | Red flags |
|------|------:|-----------|
| `website-cost-small-business-michigan-2026` | 1260 | long, repeated pricing |
| `website-as-a-service-vs-buy-outright` | 1325 | long |
| `google-business-profile-checklist-metro-detroit` | 1323 | long, pricing, geo repeat |
| `what-to-put-on-3-page-website` | 1287 | long, pricing |
| `wix-squarespace-vs-custom-website` | 1207 | long, pricing |
| `website-launch-checklist-domain-hosting` | 1192 | long |
| `first-10-google-reviews-small-business` | 1213 | long, pricing |

**Shared problems:** interchangeable tier blocks; March 2026 placeholder `published` dates; search-first risk **yes**.

**Publish dates (locked — option 2):** **Stagger per guide** when each rewrite ships.

- `published` — set to the **deploy date** for that guide’s revamp (first day substantive copy is live).
- `updated` — same as `published` on first ship; bump `updated` only on later edits.
- Remove March 2026 placeholders as each guide goes out (order: cost → WaaS → 3-page → Wix → launch → GBP → reviews).

Do not backdate. Do not use one global publish date for all seven.

## Landings — **keep** (10) in `seo-pages-data.php`

Metro Detroit, Detroit, Oakland/Macomb counties, affordable Michigan, nail/hair/contractors/landscaping/fishing guides.

Light pass in Phase 2: shorten meta, add “Metro Detroit” to industry titles per SEO audit, remove awkward “search engine” filler in nail/affordable pages.

## Landings — **rewrite** (22) in `seo-pages-batch.php`

**P0 (rewrite first — longest / worst stuffing):**

- Cities: Livonia, Dearborn, Novi, Farmington Hills, Southfield, Ann Arbor (max section **326–392** words)
- Industries: cleaning services, law firms (max section **429–452**)
- Intent: `website-cost-michigan`, `website-redesign-services`, `wix-squarespace-alternative`, `google-business-profile-setup`

**P1:** Remaining batch cities + industries (Troy, Royal Oak, Sterling Heights, Warren, restaurants, dentists, plumbers-hvac, auto repair, gyms, real estate).

**Target MC:** ~120–220 words per section; one **Experience** hook per page (real geography, trade, or intake story).

**Cross-link (avoid duplicate intent):**

| Landing | Lean on guide instead of re-explaining |
|---------|----------------------------------------|
| `website-cost-michigan` | `/resources/website-cost-small-business-michigan-2026/` |
| `wix-squarespace-alternative` | `/resources/wix-squarespace-vs-custom-website/` |
| `google-business-profile-setup` | `/resources/google-business-profile-checklist-metro-detroit/` |

## Experience hooks (draft briefs — fill during Phase 1–2)

| Slug | Hook to own on-page |
|------|---------------------|
| `website-cost-small-business-michigan-2026` | Real package bands from intake; when a Sterling Heights owner outgrows three pages. |
| `google-business-profile-checklist-metro-detroit` | GBP verification walk-through; NAP match with Path A staging clients (no names without permission). |
| `web-design-services-troy` | Somerset / Big Beaver **one** concrete search pattern, not landmark laundry list. |
| `web-design-services-fishing-guides` | DJ’s Tackle / guide booking angle (with client OK). |
| `web-design-services-contractors` | Trucks, calls, Macomb job-site photos — intake reality. |

## Forbidden duplicate blocks (cut or link)

- Full Simple / Business / Growth / WaaS tier essays on non-pricing URLs.
- “Core Web Vitals,” “map pack,” and I-75 commuter clichés unless one sentence is truly local.
- Identical process paragraphs — link `/process/` instead.
- FAQ answers copied from a sibling landing or guide.

## Phase 1 — guides (content refresh done; **topics owner-led**)

Seven existing guides were rewritten (staggered publish `2026-11-12` … `2026-11-18`) and **stay live** until Ian replaces or removes them topic-by-topic in chat. Track decisions in [resource-topics.csv](resource-topics.csv).

## Next step — Phase 2

SEO landings: light pass on `seo-pages-data.php` (10), heavy rewrite on `seo-pages-batch.php` (22).

Re-run inventory after Phase 2:

```bash
php bin/content-phase0-audit.php > docs/content-revamp/phase0-inventory.csv
```
